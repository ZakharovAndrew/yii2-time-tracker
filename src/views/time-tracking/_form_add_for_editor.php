<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use ZakharovAndrew\TimeTracker\Module;
use ZakharovAndrew\TimeTracker\models\Activity;

/** @var yii\web\View $this */
/** @var ZakharovAndrew\TimeTracker\models\TimeTracking $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="time-tracking-form">

    <?php $form = ActiveForm::begin(['action' => '/timetracker/time-tracking/add']); ?>

    <?= $form->field($model, 'activity_id')->dropDownList(Activity::getActivityByUserId($user_id, true), ['prompt'=>'', 'class' => 'form-control form-select']) ?>

    <?= $this->render('_activity_properties', [
        'properties' => \ZakharovAndrew\TimeTracker\models\ActivityProperty::find()->orderBy('pos ASC')->all(),
        'activity_id' => null,
        'user_id' => $model->user_id
    ]) ?>
    
    <div class="form-group">
        <label><?= Module::t('Activity time') ?></label>
        <?= Html::input('time', 'activity_time', '', ['class' => 'form-control', 'id'=> "timetracking-activity_time"]); ?>
    </div>
    
    <?= $form->field($model, 'datetime_at')->hiddenInput()->label(false) ?>
    <?= $form->field($model, 'user_id')->hiddenInput()->label(false) ?>
    <?= $form->field($model, 'comment')->textarea(['rows' => '6']) ?>
    
    <div class="form-group">
        <?= Html::submitButton(Module::t('Save'), ['class' => 'btn btn-success']) ?>
        
    </div>

    <?php ActiveForm::end(); ?>

</div>
