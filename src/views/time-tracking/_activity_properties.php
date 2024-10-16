<?php

use yii\helpers\Html;
use ZakharovAndrew\TimeTracker\models\ActivityProperty;
use ZakharovAndrew\user\models\UserSettings;

/** @var yii\web\View $this */
/** @var ZakharovAndrew\TimeTracker\ActivityProperty $property */

$user_settings = \yii\helpers\ArrayHelper::map(UserSettings::find()
            ->select(['user_settings_config.code', 'user_settings.values'])
            ->leftJoin('user_settings_config', 'user_settings.setting_config_id = user_settings_config.id')
            ->where([
                'user_settings.user_id' => \Yii::$app->user->id
            ])
            ->asArray()
            ->all(), 'code', 'values');
?>

<?php foreach ($properties as $property) {
    // property value
    $value = $property->getUserPropertyValue($activity_id);

    $hide = false;
    
    if (is_array($property->params) && isset($property->params['user_property'])) {
        foreach ($property->params['user_property'] as $param) {
            if (empty($param['comparison'])) {
                continue;
            }
            
            if (!isset($user_settings[$param['name']])) {
                $hide = true;
                continue;
            }
            
            //compare
            switch ($param['comparison']) {
                case '=':
                    $hide = ($user_settings[$param['name']] != $param['value']);
                    break;
            }
        }
    }
    
    if ($hide) {
        continue;
    }
?>
    <div class="form-group">
        <label><?= $property->name ?></label>
        <?php
        if ($property->type == ActivityProperty::TYPE_STRING && !empty($property->getValues())) {
            echo Html::dropDownList($property->id, $value, $property->getValues(), [
                    'id' => 'property-'.$property->id,
                    'class' => 'form-control',
                    'prompt' => ''
                ]);
        } else if ($property->type == ActivityProperty::TYPE_CHECKBOX) {
            echo Html::checkbox($property->id, $value);
        } else {
            // determine the type
            $inputType = 'text';
            if ($property->type == ActivityProperty::TYPE_TIME) {
                $inputType = 'time';
            } else if ($property->type == ActivityProperty::TYPE_DATE) {
                $inputType = 'date';
            }
            echo Html::input($inputType, $property->id, $value, ['id' => 'property-'.$property->id, 'class' => 'form-control']);
        }?>
    </div>
<?php } ?>
