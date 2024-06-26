<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use ZakharovAndrew\TimeTracker\Module;

/** @var yii\web\View $this */
/** @var ZakharovAndrew\TimeTracker\models\TimeTracking $model */

$this->title = Module::t('Add Activity');
$this->params['breadcrumbs'][] = ['label' => Module::t('Time Tracking'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="time-tracking-create">

    <?php if (Yii::$app->getModule('timetracker')->showTitle) {?><h1><?= Html::encode($this->title) ?></h1><?php } ?>

    <div class="time-tracking-form">

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'activity_id')->dropDownList(\ZakharovAndrew\TimeTracker\models\Activity::getActivityByUserId(Yii::$app->user->id), ['prompt'=>'', 'class' => 'form-control form-select']) ?>

        <?= $form->field($model, 'comment')->textInput(['maxlength' => true]) ?>

        <div class="form-group">
            <?= Html::submitButton(Module::t('Save'), ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>
</div>
