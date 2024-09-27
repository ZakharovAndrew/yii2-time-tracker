<?php

namespace ZakharovAndrew\TimeTracker\models;

use Yii;
use ZakharovAndrew\TimeTracker\Module;
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
    
    public function beforeSave($insert)
    {
        $this->datetime_update = date('Y-m-d H:i:s');
        $this->who_changed = Yii::$app->user->id;
        
        return parent::beforeSave($insert);
    }
}
