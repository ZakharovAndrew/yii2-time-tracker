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

    <?php $form = ActiveForm::begin(['action' => 'create']); ?>

    <?= $form->field($model, 'activity_id')->dropDownList(Activity::getActivityByUserId($user_id ?? Yii::$app->user->id), ['prompt'=>'', 'class' => 'form-control form-select']) ?>

    <?= $form->field($model, 'comment')->textarea(['rows' => '6']) ?>

    <div class="form-group">
        <?= Html::submitButton(Module::t('Save'), ['class' => 'btn btn-success']) ?>
        
    </div>

    <?php ActiveForm::end(); ?>

</div>
