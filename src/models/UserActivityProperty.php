<?php

namespace ZakharovAndrew\TimeTracker\models;

use Yii;

/**
 * This is the model class for table "user_activity_property".
 *
 * @property int $id
 * @property int|null $user_id
 * @property int|null $property_id
 * @property string|null $values
 *
 */
class UserActivityProperty extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user_activity_property';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'property_id'], 'integer'],
            [['values'], 'string', 'max' => 500],
            [['property_id'], 'exist', 'skipOnError' => true, 'targetClass' => ActivityProperty::class, 'targetAttribute' => ['property_id' => 'id']],
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
            'property_id' => 'Property ID',
            'values' => 'Values',
        ];
    }

    /**
     * Gets query for [[ActivityProperty]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getActivityProperty()
    {
        return $this->hasOne(ActivityProperty::class, ['id' => 'property_id']);
    }
    
    /**
     * Saving a property value
     * @param int $user_id
     * @param int $property_id
     * @param string $values
     */
    public static function saveValue($user_id, $property_id, $values)
    {
        $params = [
            'user_id' => $user_id,
            'property_id' => $property_id
        ];
        
        // maybe the setting already exists
        $model = static::find()->where($params)->one();
        
        if (!$model) {
            $model = new UserActivityProperty($params);
        }
        
        // change value
        $model->values = $values;
        $model->save();
    }
}
