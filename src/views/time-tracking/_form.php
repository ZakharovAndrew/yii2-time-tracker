<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use ZakharovAndrew\TimeTracker\Module;

/** @var yii\web\View $this */
/** @var ZakharovAndrew\TimeTracker\models\TimeTracking $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="time-tracking-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'user_id')->hiddenInput()->label(false) ?>
    
    <?= $form->field($model, 'activity_id')->dropDownList($activity, ['prompt'=>'', 'class' => 'form-control form-select']) ?>

    <?= $form->field($model, 'datetime_at')->textInput() ?>

    <?= $form->field($model, 'comment')->textarea(['rows' => '6']) ?>
    
    <?= $this->render('_activity_properties', [
        'properties' => $properties,
        'activity_id' => $model->activity_id,
        'user_id' => \Yii::$app->user->id
    ]) ?>

    <div class="form-group">
        <?= Html::submitButton(Module::t('Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
