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
 * @property string|null $params
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
            [['params'], 'safe'],
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
    
    public static function getComparisonList()
    {
        return [
            '=' => '=',
            '>' => '>',
            '<' => '<',
            '<>' => Module::t('not equal'),
            'contains' => Module::t('contains')
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
    
    public function load($data, $form_name = null)
    {
        $result = parent::load($data, $form_name);
        
        $this->params = json_encode($this->params, JSON_UNESCAPED_UNICODE);
        
        return $result;
    }
    
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if (is_array($this->params)) {
                $this->params = json_encode($this->params, JSON_UNESCAPED_UNICODE);
            }

            return true;
        } else {
            return false;
        }
    }
    
    public function afterFind()
    {
        parent::afterFind();
        $this->params = json_decode($this->params ?? '', JSON_UNESCAPED_UNICODE);
        return true;
    }    
}
