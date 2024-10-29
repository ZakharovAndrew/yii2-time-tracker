<?php

namespace ZakharovAndrew\TimeTracker\models;

use Yii;
use ZakharovAndrew\TimeTracker\Module;
use ZakharovAndrew\TimeTracker\models\Activity;
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
            [['user_id', 'activity_id', 'who_changed'], 'integer'],
            ['activity_id', 'required'],
            [['datetime_at', 'datetime_update'], 'safe'],
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
    
    static public function getUserActivity($user_id)
    {
        return TimeTracking::find()
                ->where(['>', 'datetime_at', date('Y-m-d 00:00:00')])
                ->andWhere(['user_id' => $user_id])
                ->orderBy('datetime_at ASC')
                ->all();
    }
    
    static public function getUserLastActivity($user_id)
    {
        return TimeTracking::find()
                ->where(['>', 'datetime_at', date('Y-m-d 00:00:00')])
                ->andWhere(['user_id' => $user_id])
                ->orderBy('datetime_at DESC')
                ->one();
    }
    
    static public function userRolesForViewingStatistics()
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
        if (!is_array($roles) || count($roles) == 0) {
            $query = static::find()
                ->leftJoin('users', 'users.id = time_tracking.user_id')
                ->andWhere(['>', 'datetime_at', $start_day])
                ->andWhere(['<=', 'datetime_at', $stop_day])
                //->andWhere('users.id' => $users)
                ->orderBy('datetime_at');
        } else {
            $query = static::find()
                ->leftJoin('user_roles', 'user_roles.user_id = time_tracking.user_id')
                ->leftJoin('roles', 'user_roles.role_id = roles.id')
                ->leftJoin('users', 'users.id = time_tracking.user_id')
                ->where(['roles.code' => $roles])
                ->andWhere(['>', 'datetime_at', $start_day])
                ->andWhere(['<=', 'datetime_at', $stop_day])
                //->andWhere('LIKE', 'users.name', $username)
                ->orderBy('datetime_at');
        }
        
        if (!empty($user)) {
            $query->andWhere(['time_tracking.user_id' => $user]);
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
    
    public function isWorkDay()
    {
        return $this->activity_id == Activity::WORK_START;
    }
    
    public function isWorkBreak()
    {
        return $this->activity_id == Activity::WORK_BREAK;
    }
    
    public function beforeSave($insert)
    {
        $this->datetime_update = date('Y-m-d H:i:s');
        $this->who_changed = Yii::$app->user->id;
        
        return parent::beforeSave($insert);
    }
}
