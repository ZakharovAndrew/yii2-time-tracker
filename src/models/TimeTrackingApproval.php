<?php

namespace ZakharovAndrew\TimeTracker\models;

use Yii;
use ZakharovAndrew\TimeTracker\Module;
use ZakharovAndrew\user\models\User;

/**
 * This is the model class for table "time_tracking_approval".
 *
 * @property int $id
 * @property int $user_id
 * @property string $approval_date
 * @property int $approver_id
 * @property string $approved_at
 */
class TimeTrackingApproval extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'time_tracking_approval';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'approval_date', 'approver_id'], 'required'],
            [['user_id', 'approver_id'], 'integer'],
            [['approval_date', 'approved_at'], 'safe'],
            [['user_id', 'approval_date'], 'unique', 'targetAttribute' => ['user_id', 'approval_date']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => Module::t('User'),
            'approval_date' => Module::t('Approval Date'),
            'approver_id' => Module::t('Approver'),
            'approved_at' => Module::t('Approved At'),
        ];
    }
    
    /**
     * Gets the user
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }
    
    /**
     * Gets the approver
     *
     * @return \yii\db\ActiveQuery
     */
    public function getApprover()
    {
        return $this->hasOne(User::class, ['id' => 'approver_id']);
    }
    
    /**
     * Approve the day
     *
     * @param int $userId
     * @param int $approverId
     * @param string $approvalDate
     * @return bool
     */
    public static function approve($userId, $approverId, $approvalDate = null)
    {
        $model = new static();
        $model->user_id = $userId;
        $model->approver_id = $approverId;
        
        if (isset($approvalDate)) {
            $model->approval_date = $approvalDate;
        }
        
        return $model->save();
    }
    
    /**
     * Check if day is approved
     *
     * @param int $userId
     * @param string $date
     * @return bool
     */
    public static function isDayApproved($userId, $date)
    {
        return static::find()
            ->where(['user_id' => $userId, 'approval_date' => $date])
            ->exists();
    }
    
    /**
     * Get approved days for user
     *
     * @param int $userId
     * @param string $startDate
     * @param string $endDate
     * @return array
     */
    public static function getApprovedDays($userId, $startDate, $endDate)
    {
        return static::find()
            ->where(['user_id' => $userId])
            ->andWhere(['between', 'approval_date', $startDate, $endDate])
            ->orderBy(['approval_date' => SORT_DESC])
            ->all();
    }
}