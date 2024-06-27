<?php

namespace ZakharovAndrew\TimeTracker\models;

use Yii;

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
    
    const START_DAY = 1000000;
    const STOP_DAY  = 1000001;
    
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
            'activity_id' => 'Activity ID',
            'datetime_at' => 'Datetime At',
            'comment' => 'Comment',
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
