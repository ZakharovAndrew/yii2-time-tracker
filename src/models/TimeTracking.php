<?php

namespace ZakharovAndrew\TimeTracker\models;

use Yii;
use ZakharovAndrew\TimeTracker\Module;
use ZakharovAndrew\TimeTracker\models\Activity;
use ZakharovAndrew\TimeTracker\models\UserActivityProperty;
use ZakharovAndrew\user\models\Roles;
use \yii\helpers\ArrayHelper;

/**
 * This is the model class for table "time_tracking".
 *
 * @property int $id
 * @property int|null $user_id
 * @property int|null $activity_id
 * @property string|null $datetime_at
 * @property string|null $comment
 */
class TimeTracking extends \yii\db\ActiveRecord
{   
    public $change_logging = true;
    public $afterUpdateFunctionEnabled = true;
    public $afterCreateFunctionEnabled = true;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'time_tracking';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'activity_id', 'who_changed', 'duration'], 'integer'],
            ['activity_id', 'required'],
            [['datetime_at', 'datetime_update', 'datetime_finish'], 'safe'],
            [['comment'], 'string', 'max' => 500],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'activity_id' => Module::t('Activity'),
            'datetime_at' => Module::t('Activity time'),
            'datetime_update' => Module::t('Update time'),
            'comment' => Module::t('Comment'),
        ];
    }
    
	/**
	 * Returns all user activities for today (from 00:00:00).
	 *
	 * @param int $user_id
	 * @return static[]
	 */
	public static function getUserActivity(int $user_id): array
	{
	    return static::find()
	        ->where(['>', 'datetime_at', date('Y-m-d 00:00:00')])
	        ->andWhere(['user_id' => $user_id])
	        ->orderBy('datetime_at ASC')
	        ->all();
	}
    
	/**
	 * Returns the latest user activity for today (or null if none).
	 *
	 * @param int $user_id
	 * @return static|null
	 */
	public static function getUserLastActivity(int $user_id): ?self
	{
	    return static::find()
	        ->where(['>', 'datetime_at', date('Y-m-d 00:00:00')])
	        ->andWhere(['user_id' => $user_id])
	        ->orderBy('datetime_at DESC')
	        ->one();
	}

	/**
     * @return array<string, string> Roles allowed for viewing statistics, filtered by current user's roles
     */
    public static function userRolesForViewingStatistics(): array
    {
        $list = Yii::$app->getModule('timetracker')->availableRolesForViewingStatistics;
        $user_roles = ArrayHelper::map(Roles::getRolesByUserId(Yii::$app->user->id), 'code', 'code');
        
        return array_intersect_key($list, $user_roles);
    }
    
    /**
     * Get a list of activities for the selected period
     * 
     * @param string $start_day - Start of interval (format Y-m-d)
     * @param string $stop_day - Finish of interval (format Y-m-d)
     * @param mixed $user - List of user IDs
     * @param array $roles - List of user role IDs
     */
    public static function getActivityList($start_day, $stop_day, $user = null, $roles = [])
    {
        $query = static::find()
            ->leftJoin('users', 'users.id = time_tracking.user_id')
            ->where(['>', 'datetime_at', $start_day])
            ->andWhere(['<=', 'datetime_at', $stop_day])
            ->orderBy('datetime_at');
        
        if (!empty($user)) {
            $query->andWhere(['time_tracking.user_id' => $user]);
        }
        
        if (!empty($roles)) {
            $query->leftJoin('user_roles', 'user_roles.user_id = time_tracking.user_id')
                ->leftJoin('roles', 'user_roles.role_id = roles.id')
                ->andWhere(['roles.code' => $roles]);
        }
        
        return $query->all();
    }
    
    /**
     * Get a list of activities by day for the selected period
     * 
     * @param string $start_day - Start of interval (format Y-m-d)
     * @param string $stop_day - Finish of interval (format Y-m-d)
     * @param mixed $user - List of user IDs
     * @param array $roles - List of user role IDs
     */
    public static function getActivityByDay($start_day, $stop_day, $user = [], $roles = [])
    {
        $model = static::getActivityList($start_day, $stop_day, $user, $roles = []);
        
        $timeline = [];
        $users = [];
        
        foreach ($model as $item) {
            $item_name = date('Y-m-d', strtotime($item->datetime_at));
            $timeline[$item_name][$item->user_id][] = $item;
            $users[$item->user_id] = $item->user_id;
        }
        
        return [
            'days' => $timeline,
            'users_id' => array_keys($users)
        ];
    }
    
    /**
     * Get user activities grouped by day
     * 
     * @param string $start_day Start date (Y-m-d)
     * @param string $stop_day End date (Y-m-d)
     * @param int|array $user User ID or array of user IDs
     * @return array Activities grouped by date [ '2024-01-01' => [activities], ... ]
     */
    public static function userTimeline(string $start_day, string $stop_day, $user)
    {
        $activities = static::getActivityList($start_day, $stop_day, $user);
        
        $timeline = [];
        
        foreach ($activities  as $activity) {
            $date = date('Y-m-d', strtotime($activity->datetime_at));
            $timeline[$date][] = $activity;
        }
        
        return $timeline;
    }

	/**
     * @return bool
     */
    public function isWorkStart(): bool
    {
        return $this->activity_id == Activity::WORK_START;
    }

	/**
     * @return bool
     */
    public function isWorkStop(): bool
    {
        return $this->activity_id == Activity::WORK_STOP;
    }

	/**
     * @return bool
     */
    public function isWorkBreak(): bool
    {
        return $this->activity_id == Activity::WORK_BREAK;
    }
    
    public function beforeSave($insert)
    {
        $function = Yii::$app->getModule('timetracker')->beforeSaveFunction;
        
        if (!empty($function) && is_callable($function)) {
            $function($this);
        }
        
        if ($this->change_logging) {
            $this->datetime_update = date('Y-m-d H:i:s');
            $this->who_changed = Yii::$app->user->id;
        }
        
        $datetime_at = $this->datetime_at ?? date('Y-m-d H:i:s');

        $after = self::find()
            ->where(['user_id' => $this->user_id])
            ->andWhere(["or", ['>', 'datetime_at', $datetime_at], ['and', ['=', 'datetime_at', $datetime_at], ['>', 'id', $this->id]]])
            ->andWhere(['date(datetime_at)' => date('Y-m-d', strtotime($datetime_at))])            
            ->andFilterWhere(['!=', 'id', $this->id])
            ->orderBy('datetime_at ASC, id ASC')
            ->one();
        
        if ($after) {
            $this->datetime_finish = $after->datetime_at;
            $this->duration = strtotime($this->datetime_finish) - strtotime($datetime_at);
        } else {
            $this->datetime_finish = NULL;
            $this->duration = NULL;
        }
        
        return parent::beforeSave($insert);
    }
    
    public function afterSave($insert, $changedAttributes)
    {
        $datetime_at = $this->datetime_at ?? date('Y-m-d H:i:s');

        $before = self::find()
        ->where(['user_id' => $this->user_id])
        ->andWhere(["or", ['<', 'datetime_at', $datetime_at], ['and', ['=', 'datetime_at', $datetime_at], ['<', 'id', $this->id]]])
        ->andWhere(['date(datetime_at)' => date('Y-m-d', strtotime($datetime_at))])
	->andFilterWhere(['!=', 'id', $this->id])
        ->orderBy('datetime_at DESC, id DESC')
        ->one();
        
        if ($before && $before->datetime_finish != $datetime_at) {
            $before->datetime_finish = $datetime_at;
            $before->change_logging = false;
            $before->save();
            $this->duration = strtotime($before->datetime_finish) - strtotime($before->datetime_at);
            $this->change_logging = false;
        }
        
        self::setUserLastActivity($this->user_id);
        
        if ($insert && $this->afterCreateFunctionEnabled) {
            $this->afterCreateFunction();
        } elseif ($this->afterUpdateFunctionEnabled) {
            $this->afterUpdateFunction($changedAttributes ?? []);
        }
        
        parent::afterSave($insert, $changedAttributes);
    }

	/**
     * @param array $changedAttributes
     */
    public function afterUpdateFunction($changedAttributes)
    {
        $afterUpdateFunction = Yii::$app->getModule('timetracker')->afterUpdateFunction;

        if (!empty($afterUpdateFunction) && is_callable($afterUpdateFunction)) {
            $afterUpdateFunction($this, $changedAttributes, true);
        }
    }
    
    public function afterCreateFunction()
    {
        $afterCreateFunction = Yii::$app->getModule('timetracker')->afterCreateFunction;
        
        if (!empty($afterCreateFunction) && is_callable($afterCreateFunction)) {
            $afterCreateFunction($this);
        }
    }


    public function afterDelete()
    {
        $datetime_at = $this->datetime_at ?? date('Y-m-d H:i:s');
        
        parent::afterDelete();
		
        $before = self::find()
            ->where(['user_id' => $this->user_id])
            ->andWhere(['<', 'datetime_at', $datetime_at])
            ->andWhere(['date(datetime_at)' => date('Y-m-d', strtotime($datetime_at))])
            ->andFilterWhere(['!=', 'id', $this->id])
            ->orderBy('datetime_at DESC, id DESC')
            ->one();

        if ($before) {
            $after = self::find()
                ->where(['user_id' => $this->user_id])
                ->andWhere(['>', 'datetime_at', $datetime_at])
                ->andWhere(['date(datetime_at)' => date('Y-m-d', strtotime($datetime_at))])
		->andFilterWhere(['!=', 'id', $this->id])
                ->orderBy('datetime_at ASC, id ASC')			
                ->one();

            $new_datetime_finish = $after ? $after->datetime_at : null;
            if($before->datetime_finish != $new_datetime_finish) {
                $before->datetime_finish = $new_datetime_finish;
                $before->change_logging = false;
                $before->save();
            }			
        }
        
        self::setUserLastActivity($this->user_id);
        
        $afterDeleteFunction = Yii::$app->getModule('timetracker')->afterDeleteFunction;

        if (!empty($afterDeleteFunction) && is_callable($afterDeleteFunction)) {
            $afterDeleteFunction($this);
        }
    }
    
    public static function getTopActivitiesQuery($startDate, $limit = 10)
    {
        return static::find()
            ->alias('t')
            ->select([
                't.activity_id', 
                'a.name', 
                'cnt_users' => 'COUNT(DISTINCT(user_id))', 
                'cnt' => 'COUNT(*)', 
                'duration' => 'SUM(duration)'
            ])
            ->leftJoin(['a' => Activity::tableName()], 'a.id = t.activity_id')
            ->where(['NOT IN', 't.activity_id', [
                Activity::WORK_START, 
                Activity::WORK_STOP, 
                Activity::WORK_BREAK
            ]])
            ->andWhere(['>', 'datetime_at', $startDate])
            ->groupBy('t.activity_id, a.name')
            ->orderBy(['cnt' => SORT_DESC])
            ->limit($limit);
    }
    
    public static function getCountFromDate($startDate)
    {
        return static::find()
            ->where(['>', 'datetime_at', $startDate])
            ->count();
    }
    
    public static function setUserLastActivity($user_id)
    {
        $last_activity = \ZakharovAndrew\user\models\User::find()->alias('u')
            ->select([
                'u.id', 
                'tt_last.datetime_at as last_activity_time', 
                'tt_last.activity_id as last_activity_id', 
                'a.name as last_activity_name',
            ])
            ->leftJoin(['tt_last' => TimeTracking::tableName()], 'tt_last.id = (
                SELECT id 
                FROM '.TimeTracking::tableName().' 
                WHERE user_id = u.id 
                ORDER BY datetime_at DESC, id DESC 
                LIMIT 1
            )')
            ->leftJoin(['a' => Activity::tableName()], 'a.id = tt_last.activity_id')
            ->where(['u.id' => $user_id])
            ->asArray()
            ->one();
        
        $users = static::getUsersWithLastActivity();
        
        $users[$user_id] = $last_activity;
        
        Yii::$app->cache->set('users_with_last_activity', $users, 600);
    }
    
    public static function getUsersWithLastActivity()
    {
        return Yii::$app->cache->getOrSet('users_with_last_activity', function () {
            return ArrayHelper::index(\ZakharovAndrew\user\models\User::find()->alias('u')
            ->select([
                'u.id', 
                'tt_last.datetime_at as last_activity_time', 
                'tt_last.activity_id as last_activity_id', 
                'a.name as last_activity_name',
            ])
            ->leftJoin(['tt_last' => TimeTracking::tableName()], 'tt_last.id = (
                SELECT id 
                FROM '.TimeTracking::tableName().' 
                WHERE user_id = u.id 
                ORDER BY datetime_at DESC, id DESC 
                LIMIT 1
            )')
            ->leftJoin(['a' => Activity::tableName()], 'a.id = tt_last.activity_id')
            ->asArray()
            ->all(), 'id');
        }, 600);
    }
    
    public function getUserActivityProperties()
    {
        return UserActivityProperty::find()
                ->where(['activity_id' => $this->id])
                ->andWhere(['user_id' => $this->user_id])->all();
    }
    
    /**
     * Calculate total working hours and break time for activities
     * 
     * @param array|TimeTracking[] $activities List of activities
     * @return array [workTime, breakTime] in seconds
     */
    public static function calculateWorkBreakTime($activities)
    {
        $workTime = 0;
        $breakTime = 0;
        $activityCount = count($activities);

        foreach ($activities as $i => $activity) {
            $nextActivityTime = ($i === $activityCount - 1) 
                ? time() 
                : strtotime($activities[$i + 1]->datetime_at);

            $activityTime = $nextActivityTime - strtotime($activity->datetime_at);

            if (!$activity->isWorkStop() && !$activity->isWorkBreak()) {
                $workTime += $activityTime;
            }

            if ($activity->isWorkBreak()) {
                $breakTime += $activityTime;
            }
        }

        return [$workTime, $breakTime];
    }
		
	/**
	 * Get heatmap data for Chart.js matrix chart
	 * 
	 * @param string $start_date
	 * @param string $stop_date
	 * @param array|null $user_ids
	 * @return array
	 */
	public static function getHeatmapData($start_date, $stop_date, $user_ids = null)
	{
	    $query = self::find()
	        ->where(['>=', 'datetime_at', $start_date . ' 00:00:00'])
	        ->andWhere(['<=', 'datetime_at', $stop_date . ' 23:59:59'])
	        ->andWhere(['IS NOT', 'datetime_finish', null])
	        ->orderBy(['user_id' => SORT_ASC, 'datetime_at' => SORT_ASC]);
	    
	    if ($user_ids !== null) {
	        $query->andWhere(['user_id' => $user_ids]);
	    }
	    
	    $records = $query->all();
	    
	    // Split each record by hour
	    $hourly = [];
	    $userNames = [];
	    
	    foreach ($records as $record) {
	        $start = strtotime($record->datetime_at);
	        $end = strtotime($record->datetime_finish);
	        $userId = $record->user_id;
	        
	        // Get user name from relation if set
	        $userName = $record->user->name ?? "User #$userId";
	        $userNames[$userId] = $userName;
	        
	        $startHour = (int)date('G', $start);
	        $endHour = (int)date('G', $end);
	        
	        if ($startHour == $endHour) {
	            // Everything within the same hour
	            $minutes = ($end - $start) / 60;
	            $hourly[$userId][$startHour] = ($hourly[$userId][$startHour] ?? 0) + $minutes;
	        } else {
	            // Distribute across hours
	            $cursor = $start;
	            while ($cursor < $end) {
	                $hour = (int)date('G', $cursor);
	                $nextHour = strtotime(date('Y-m-d H:00:00', $cursor) . ' +1 hour');
	                $segmentEnd = min($nextHour, $end);
	                $minutes = ($segmentEnd - $cursor) / 60;
	                $hourly[$userId][$hour] = ($hourly[$userId][$hour] ?? 0) + $minutes;
	                $cursor = $segmentEnd;
	            }
	        }
	    }
	    
	    // Prepare data for Chart.js matrix
	    $dataset = [];
	    foreach ($hourly as $userId => $hours) {
	        foreach ($hours as $hour => $minutes) {
	            $dataset[] = [
	                'x' => $hour,
	                'y' => $userNames[$userId],
	                'v' => round($minutes, 1),
	            ];
	        }
	    }
	    
	    return $dataset;
	}
}
