<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use ZakharovAndrew\TimeTracker\Module;
use ZakharovAndrew\TimeTracker\models\Activity;

/** @var yii\web\View $this */
/** @var ZakharovAndrew\TimeTracker\models\TimeTracking $model */
/** @var yii\widgets\ActiveForm $form */

$saving = Module::t('Saving');

$script = <<< JS
        
$('#saveButton').click(function() {
    $('#saveButton').html('$saving...');
    $('#saveButton').addClass('disabled');
    console.log('CLICK!');
});
JS;

$this->registerJs($script, yii\web\View::POS_READY);
?>

<div class="time-tracking-form">

    <?php $form = ActiveForm::begin(['action' => 'create']); ?>

    <?= $form->field($model, 'activity_id')->dropDownList(Activity::getActivityByUserId($user_id ?? Yii::$app->user->id), ['prompt'=>'', 'class' => 'form-control form-select']) ?>

    <?= $this->render('_activity_properties', [
        'properties' => $properties,
        'activity_id' => null,
        'user_id' => \Yii::$app->user->id
    ]) ?>
    
    <?= $form->field($model, 'comment')->textarea(['rows' => '6']) ?>
    
    <div class="form-group">
        <?= Html::submitButton(Module::t('Save'), ['id' => 'saveButton', 'class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>
