<?php

use yii\helpers\Html;
use ZakharovAndrew\TimeTracker\models\ActivityProperty;
use ZakharovAndrew\TimeTracker\models\Activity;
use ZakharovAndrew\user\models\UserSettings;

/** @var yii\web\View $this */
/** @var ZakharovAndrew\TimeTracker\ActivityProperty $property */

$user_settings = \yii\helpers\ArrayHelper::map(UserSettings::find()
            ->select(['user_settings_config.code', 'user_settings.values'])
            ->leftJoin('user_settings_config', 'user_settings.setting_config_id = user_settings_config.id')
            ->where([
                'user_settings.user_id' => $user_id
            ])
            ->asArray()
            ->all(), 'code', 'values');

$js_logic = '';

?>

<?php foreach ($properties as $property) {
    // property value
    $value = $property->getUserPropertyValue($activity_id, $user_id);
    
    $show = true;
       
    if (is_array($property->params) && isset($property->params['user_property'])) {
        foreach ($property->params['user_property'] as $param) {
            if (empty($param['comparison'])) {
                continue;
            }
            
            if (!isset($user_settings[$param['name']]) && $param['logic'] == 'AND') {
                $show = false;
                continue;
            }
            
            $user_settings_value = $user_settings[$param['name']] ?? null;
            
            //compare
            switch ($param['comparison']) {
                case '=':
                    $compare = ($user_settings_value == $param['value']);
                    break;
                case '<>':
                    $compare = ($user_settings_value != $param['value']);
                    break;
                case '>':
                    $compare = ((int)$user_settings_value > (int)$param['value']);
                    break;
                case '<':
                    $compare = ((int)$user_settings_value < (int)$param['value']);
                    break;
            }
            
            if ($param['logic'] == 'OR') {
                $show = $show || $compare;
            } else {
                $show = $show && $compare;
            }
        }
    }
    
    if (!$show) {
        continue;
    }
    
    $js_logic1 = '';
    if (is_array($property->params) && isset($property->params['activity_property'])) {
        $comparison = [
            '=' => '==',
            '<>' => '!=',
            '>' => '>',
            '<' => '<',
        ];
        $property_logic = [];
        foreach ($property->params['activity_property'] as $param) {
            if (empty($param['comparison']) || empty($param['name'])) {
                continue;
            }
            
            if (isset($comparison[$param['comparison']])) {
                $rule = '($("#property-'.$param['name'].'").val()'.$comparison[$param['comparison']].$param['value'].' )';
            } else if ($param['comparison'] == 'checked') {
                $rule = '($("#property-'.$param['name'].'").is(":checked"))';
            } else if ($param['comparison'] == 'contain') {
                $rule = '($("#property-'.$param['name'].'").val().includes("'.$param['value'].'") )';
            }                    
            
            if (count($property_logic) == 0) {
                $property_logic[] = $rule;
            } else {
                if ($param['logic'] == 'OR') {
                    $property_logic[] = '||'.$rule;
                } else {
                    $property_logic[] = '&&'.$rule;
                }
            }
            
        }
        if (count($property_logic) > 0) {
            $js_logic1 = 'if ('.implode('', $property_logic).') {$("#property-'.$property->id.'").parent().show()} else {$("#property-'.$property->id.'").parent().hide()}'."\n";
        }
    }
    
    // show when activity is selected
    $list = $property->params['show_when_activity'] ?? [];
    if (count($list) > 0) {
        $js_logic1 = $js_logic1 == '' ? '$("#property-'.$property->id.'").parent().show();' : $js_logic1;
        $js_logic .= '$("#timetracking-activity_id").on("change keyup", function (){if ([' . implode(',',$list) . '].includes($("#timetracking-activity_id").val() * 1)) { console.log("Типа должен показывать"); '.$js_logic1.'} else {console.log("Типа НЕ должен показывать");$("#property-'.$property->id.'").parent().hide();}  });';
    } else {
        $js_logic .= '$("#property-'.$property->id.'").parent().hide();';
    }
?>
    <div class="form-group">
        <label><?= $property->name ?></label>
        <?php
        $required = ($property->required ?? false) == true;
        if ($property->type == ActivityProperty::TYPE_STRING && !empty($property->getValues())) {
            echo Html::dropDownList($property->id, $value, $property->getValues(), [
                    'id' => 'property-'.$property->id,
                    'class' => 'form-control activity-property',
                    'prompt' => '',
                    'required' => $required
                ]);
        } else if ($property->type == ActivityProperty::TYPE_CHECKBOX) {
            echo Html::checkbox($property->id, $value, ['id' => 'property-'.$property->id, 'class' => 'activity-property', 'required' => $required]);
        } else {
            // determine the type
            $inputType = 'text';
            if ($property->type == ActivityProperty::TYPE_TIME) {
                $inputType = 'time';
            } else if ($property->type == ActivityProperty::TYPE_DATE) {
                $inputType = 'date';
            }
            echo Html::input($inputType, $property->id, $value, ['id' => 'property-'.$property->id, 'class' => 'form-control activity-property', 'required' => $required]);
        }?>
    </div>
<?php }

// JS SCRIPT for show/hide the properties

$script = <<< JS
function activity_property_check() {
    $js_logic
}
activity_property_check();
        
$('.activity-property').on('change keyup', function () {
    activity_property_check();
});
JS;

$this->registerJs($script, yii\web\View::POS_READY);
