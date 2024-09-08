<?php

namespace ZakharovAndrew\TimeTracker\models;

use Yii;
use ZakharovAndrew\TimeTracker\Module;
use \yii\helpers\ArrayHelper;

/**
 * This is the model class for table "time_tracking_activity".
 *
 * @property int $id
 * @property string|null $name
 */
class Activity extends \yii\db\ActiveRecord
{
    const START_DAY = 1000000;
    const STOP_DAY  = 1000001;
    const BREAK_DAY = 1000002;
    
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'time_tracking_activity';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'color'], 'string', 'max' => 100],
            [['comment_templates', 'hint'], 'string'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => Module::t('Name'),
            'comment_templates' => Module::t('Comment Templates'),
            'color' => Module::t('Color'),
            'hint' => Module::t('Hint'),
        ];
    }
    
    static public function getDropdownList()
    {
        return ArrayHelper::map(static::find()->asArray()->all(), 'id', 'name');
    }
    
    static public function userActivity($user_id, $additionalCondition = null)
    {
        $query = static::find()
                ->where('id in (select activity_id as id FROM time_tracking_role_activity a WHERE a.role_id in (SELECT role_id FROM user_roles WHERE user_id = '.(int)$user_id.' ))');
        
        if ($additionalCondition !== null) {
            $query->andWhere($additionalCondition);
        }

        return $query->asArray()->all();
    }
    
    static public function getActivityByUserId($user_id)
    {        
        return ArrayHelper::map(static::userActivity($user_id), 'id', 'name');
    }
    
    /**
     * Get hints for activities available to the user
     */
    static public function getHintsActivityByUserId($user_id)
    {
        return ArrayHelper::map(static::userActivity($user_id, "hint <> '' AND hint IS NOT NULL"), 'id', 'hint');
    }
    static public function getTemplateCommentsActivityByUserId($user_id)
    {
        return ArrayHelper::map(static::userActivity($user_id, "comment_templates <> '' AND comment_templates IS NOT NULL"), 'id', 'comment_templates');
    }
    
    static public function getList()
    {
        $list = static::getDropdownList();
        
        $list[static::START_DAY] = Module::t('The begining of the work day');
        $list[static::STOP_DAY] = Module::t('End of the working day');
        $list[static::BREAK_DAY] = Module::t('Break');
        
        return $list;
    }
}
