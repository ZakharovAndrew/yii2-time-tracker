<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use ZakharovAndrew\TimeTracker\Module;

/** @var yii\web\View $this */
/** @var app\models\RoleActivity $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="role-activity-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'activity_id')->dropDownList(\ZakharovAndrew\TimeTracker\models\Activity::getDropdownList(), ['prompt'=>'', 'class' => 'form-control form-select']) ?>

    <?= $form->field($model, 'pos')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton(Module::t('Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
