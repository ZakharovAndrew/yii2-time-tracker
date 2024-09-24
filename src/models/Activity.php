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
    const WORK_START    = 1000000;
    const WORK_STOP     = 1000001;
    const WORK_BREAK    = 1000002;
    
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
    
    public static function  getDropdownList()
    {
        return ArrayHelper::map(static::find()->asArray()->all(), 'id', 'name');
    }
    
    public static function  getActivityColors()
    {
        return ArrayHelper::map(static::find()->asArray()->all(), 'id', 'color');
    }
    
    /**
     * Get a list of activities available to the user
     * 
     * @param int $user_id
     * @param string $additionalCondition
     * @return array
     */
    public static function  userActivity($user_id, $additionalCondition = null)
    {
        $query = RoleActivity::find()->alias('a')
                ->select(['t.*'])
                ->leftJoin('time_tracking_activity t', 't.id = a.activity_id')
                ->where('a.role_id in (SELECT role_id FROM user_roles WHERE user_id = '.(int)$user_id.' )')
                ->orderBy('a.pos');
        
        if ($additionalCondition !== null) {
            $query->andWhere($additionalCondition);
        }

        return $query->asArray()->all();
    }
    
    public static function  getActivityByUserId($user_id, $showAll = false)
    {   
        $list = ArrayHelper::map(static::userActivity($user_id), 'id', 'name');
        
        if ($showAll) {
            $list[static::WORK_START] = Module::t('The begining of the work day');
            $list[static::WORK_STOP] = Module::t('End of the working day');
            $list[static::WORK_BREAK] = Module::t('Break');
        }
        
        return $list;
    }
    
    /**
     * Get hints for activities available to the user
     */
    public static function  getHintsActivityByUserId($user_id)
    {
        return ArrayHelper::map(static::userActivity($user_id, "t.hint <> '' AND t.hint IS NOT NULL"), 'id', 'hint');
    }
    public static function  getTemplateCommentsActivityByUserId($user_id)
    {
        return ArrayHelper::map(static::userActivity($user_id, "t.comment_templates <> '' AND t.comment_templates IS NOT NULL"), 'id', 'comment_templates');
    }
    
    public static function  getList()
    {
        $list = static::getDropdownList();
        
        $list[static::WORK_START] = Module::t('The begining of the work day');
        $list[static::WORK_STOP] = Module::t('End of the working day');
        $list[static::WORK_BREAK] = Module::t('Break');
        
        return $list;
    }
    
    public static function timeFormat(int $time)
    {
        $hours = floor($time / 3600);
        $minutes = floor(($time % 3600) / 60);
        $seconds = $time % 60;
        
        return sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
    }
}
