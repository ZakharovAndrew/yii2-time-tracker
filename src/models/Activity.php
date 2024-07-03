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
            [['name'], 'string', 'max' => 100],
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
        ];
    }
    
    static public function getDropdownList()
    {
        return ArrayHelper::map(static::find()->asArray()->all(), 'id', 'name');
    }
    
    static public function getActivityByUserId($user_id)
    {
        $list = static::find()
                ->where('id in (select activity_id as id FROM time_tracking_role_activity a WHERE a.role_id in (SELECT role_id FROM user_roles WHERE user_id = '.$user_id.' ))')
                ->asArray()
                ->all();
        
        return ArrayHelper::map($list, 'id', 'name');
    }
    
    static public function getList()
    {
        $list = static::getDropdownList();
        
        $list[static::START_DAY] = Module::t('The begining of the work day');
        $list[static::STOP_DAY] = Module::t('End of the working day');
        
        return $list;
    }
}
