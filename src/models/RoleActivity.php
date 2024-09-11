<?php

namespace ZakharovAndrew\TimeTracker\models;

use ZakharovAndrew\TimeTracker\Module;
use \yii\helpers\ArrayHelper;

/**
 * This is the model class for table "time_tracking_role_activity".
 *
 * @property int $id
 * @property int|null $role_id
 * @property int|null $activity_id
 * @property int|null $pos
 */
class RoleActivity extends \yii\db\ActiveRecord
{
    /**
     * @var string Role title
     */
    public $role_title;
    
    /**
     * @var string Activity title
     */
    public $activity_title;
    
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'time_tracking_role_activity';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['role_id', 'activity_id', 'pos'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'role_title' => Module::t('Role'),
            'role_id' => 'Role ID',
            'activity_id' => Module::t('Activity'),
            'pos' => Module::t('Position'),
        ];
    }
    
    /**
     * Get a list of autistics for this role
     * 
     * @return array
     */
    public function getActivities()
    {
        $arr = self::find()
                ->select(['activity_id', 'time_tracking_activity.name'])
                ->leftJoin('time_tracking_activity', 'time_tracking_activity.id = activity_id')
                ->where(['role_id' => $this->role_id])
                ->orderBy('pos')
                ->asArray()
                ->all();
        
        return ArrayHelper::map($arr, 'activity_id', 'name');
    }
}
