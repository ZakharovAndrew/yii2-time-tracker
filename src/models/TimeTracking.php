<?php

namespace ZakharovAndrew\TimeTracker\models;

use Yii;
use ZakharovAndrew\TimeTracker\Module;

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
            [['user_id', 'activity_id'], 'integer'],
            ['activity_id', 'required'],
            [['datetime_at'], 'safe'],
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
            'datetime_at' => 'Datetime At',
            'comment' => Module::t('Comment'),
        ];
    }
    
    static public function getUserActivity($user_id)
    {
        return TimeTracking::find()
                ->where(['>', 'datetime_at', date('Y-m-d 00:00:00')])
                ->andWhere(['user_id' => $user_id])
                ->all();
    }
}
