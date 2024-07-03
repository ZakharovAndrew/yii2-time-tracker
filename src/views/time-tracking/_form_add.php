<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use ZakharovAndrew\TimeTracker\Module;

/** @var yii\web\View $this */
/** @var ZakharovAndrew\TimeTracker\models\TimeTracking $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="time-tracking-form">

    <?php $form = ActiveForm::begin(['action' => '/timetracker/time-tracking/create']); ?>

    <?= $form->field($model, 'activity_id')->dropDownList(\ZakharovAndrew\TimeTracker\models\Activity::getActivityByUserId(Yii::$app->user->id), ['prompt'=>'', 'class' => 'form-control form-select']) ?>

    <?= $form->field($model, 'comment')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton(Module::t('Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
