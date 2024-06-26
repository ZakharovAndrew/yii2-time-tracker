<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\RoleActivity $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="role-activity-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'role_id')->textInput() ?>

    <?= $form->field($model, 'activity_id')->textInput() ?>

    <?= $form->field($model, 'pos')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
