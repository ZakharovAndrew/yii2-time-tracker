<?php

namespace ZakharovAndrew\TimeTracker\models;

use Yii;
use ZakharovAndrew\TimeTracker\Module;

/**
 * This is the model class for table "time_tracking_activity_property".
 *
 * @property int $id
 * @property string|null $name
 * @property int|null $type
 * @property int|null $pos
 * @property string|null $values
 */
class ActivityProperty extends \yii\db\ActiveRecord
{
    
    const TYPE_INT = 1;
    const TYPE_STRING = 2;
    const TYPE_DATE = 3;
    const TYPE_TIME = 4;
    const TYPE_CHECKBOX = 5;
    
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'time_tracking_activity_property';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['type', 'pos'], 'integer'],
            [['values'], 'string'],
            [['name'], 'string', 'max' => 200],
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
            'type' => Module::t('Type'),
            'pos' => Module::t('Position'),
            'values' => Module::t('Values'),
        ];
    }
    
    public static function getTypeOfProperties()
    {
        return [
            static::TYPE_INT => Module::t('Integer'),
            static::TYPE_STRING => Module::t('String'),
            static::TYPE_DATE => Module::t('Date'),
            static::TYPE_TIME => Module::t('Time'),
            static::TYPE_CHECKBOX => Module::t('Checkbox'),
        ];
    }
    
    public function getUserPropertyValue($activity_id, $user_id = null)
    {
        if (empty($user_id)) {
            $user_id = Yii::$app->user->id;
        }
        
        $model = UserActivityProperty::find()
            ->select('values')
            ->where([
                'property_id' => $this->id,
                'activity_id' => $activity_id,
                'user_id' => $user_id
            ])->one();
        
        return $model->values ?? null;
    }
    
    public function getValues()
    {
        if (empty($this->values)) {
            return null;
        }
        
        $result = json_decode($this->values, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            // JSON is valid
            return $result;
        }
        
        $arr =  explode("\r\n", $this->values);
        
        return array_combine($arr, $arr);
    }
}
