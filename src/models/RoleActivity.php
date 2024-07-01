<?php

namespace ZakharovAndrew\TimeTracker\models;

use Yii;

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
    public $title;
    
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
            'role_id' => 'Role ID',
            'activity_id' => 'Activity ID',
            'pos' => 'Pos',
        ];
    }
}
