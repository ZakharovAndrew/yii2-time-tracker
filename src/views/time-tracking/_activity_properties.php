<?php

use yii\helpers\Html;
use ZakharovAndrew\TimeTracker\models\ActivityProperty;

/** @var yii\web\View $this */
/** @var ZakharovAndrew\TimeTracker\ActivityProperty $property */
?>

<?php foreach ($properties as $property) {?>
    <div class="form-group">
        <label><?= $property->name ?></label>
        <?php
        if ($property->type == ActivityProperty::TYPE_STRING && !empty($property->getValues())) {
            echo Html::dropDownList( $property->id, $property->getUserPropertyValue($activity_id), $property->getValues(), [
                    'id' => 'property-'.$property->id,
                    'class' => 'form-control',
                    'prompt' => ''
                ]);
        } else if ($property->type == ActivityProperty::TYPE_CHECKBOX) {
            echo Html::checkbox($property->id, $property->getUserPropertyValue($activity_id));
        } else {
            // determine the type
            $inputType = 'text';
            if ($property->type == ActivityProperty::TYPE_TIME) {
                $inputType = 'time';
            } else if ($property->type == ActivityProperty::TYPE_DATE) {
                $inputType = 'date';
            }
            echo Html::input($inputType, $property->id, $property->getUserPropertyValue($activity_id), ['id' => 'property-'.$property->id, 'class' => 'form-control']);
        }?>
    </div>
<?php } ?>