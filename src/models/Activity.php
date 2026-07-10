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
    const CACHE_DURATION = 600;
    
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
    
    /**
     * @return array<int, string>
     */
    public static function getDropdownList(): array
    {
        return ArrayHelper::map(
            static::find()->asArray()->cache(self::CACHE_DURATION)->all(),
            'id', 'name'
        );
    }
    
    /**
     * Get full list of activities including system break activity
     * 
     * @return array<int, string> Associative array of activity id => name
     */
    public static function getFullList(): array
    {
        $arr = static::getDropdownList();
        $arr[static::WORK_BREAK] = Module::t('Break');
        
        return $arr;
    }
    
    /**
     * @return array<int, string>
     */
    public static function getActivityColors(): array
    {
        return ArrayHelper::map(
            static::find()->asArray()->all(),
            'id',
            'color'
        );
    }
    
    /**
     * Get a list of activities available to the user
     * 
     * @param int $user_id
     * @param string|null $additionalCondition
     * @return array<int, array>
     */
    public static function userActivity(int $user_id, ?string $additionalCondition = null): array
    {
        $query = RoleActivity::find()->alias('a')
                ->select(['t.*'])
                ->leftJoin('time_tracking_activity t', 't.id = a.activity_id')
                ->where('a.role_id IN (SELECT role_id FROM user_roles WHERE user_id = :user_id)', [':user_id' => $user_id])
                ->orderBy('a.pos');
        
        if ($additionalCondition !== null) {
            $query->andWhere($additionalCondition);
        }

        return $query->asArray()->all();
    }
    
    /**
     * @param int $user_id
     * @param bool $showAll
     * @return array<int, string>
     */
    public static function getActivityByUserId(int $user_id, bool $showAll = false): array
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
     * 
     * @param int $user_id
     * @return array<int, string>
     */
    public static function getHintsActivityByUserId(int $user_id): array
    {
        return ArrayHelper::map(
            static::userActivity($user_id, "t.hint <> '' AND t.hint IS NOT NULL"),
            'id',
            'hint'
        );
    }

    /**
     * @param int $user_id
     * @return array<int, string>
     */
    public static function getTemplateCommentsActivityByUserId(int $user_id): array
    {
        return ArrayHelper::map(
            static::userActivity($user_id, "t.comment_templates <> '' AND t.comment_templates IS NOT NULL"),
            'id',
            'comment_templates'
        );
    }

    /**
     * Returns full activity list for dropdowns: user activities + system actions (start/stop/break).
     *
     * @return array<int, string> Activity IDs mapped to localized names.
     */
    public static function getList(): array
    {
        $list = static::getDropdownList();
        
        $list[static::WORK_START] = Module::t('The begining of the work day');
        $list[static::WORK_STOP] = Module::t('End of the working day');
        $list[static::WORK_BREAK] = Module::t('Break');
        
        return $list;
    }

    /**
     * @param int $time
     * @return string
     */
    public static function timeFormat(int $time): string
    {
        $hours = floor($time / 3600);
        $minutes = floor(($time % 3600) / 60);
        $seconds = $time % 60;
        
        return sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
    }
}
