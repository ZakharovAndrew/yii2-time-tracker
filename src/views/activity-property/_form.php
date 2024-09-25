<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use ZakharovAndrew\TimeTracker\models\ActivityProperty;

/** @var yii\web\View $this */
/** @var ZakharovAndrew\TimeTracker\models\ActivityProperty $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="activity-property-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'type')->dropDownList(ActivityProperty::getTypeOfProperties(), ['prompt' => '', 'class' => 'form-control form-select']) ?>

    <?= $form->field($model, 'pos')->textInput() ?>

    <?= $form->field($model, 'values')->textarea(['rows' => 6]) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
