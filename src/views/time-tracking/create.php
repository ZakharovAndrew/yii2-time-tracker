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

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="time-tracking-form">

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'activity_id')->dropDownList(\ZakharovAndrew\TimeTracker\models\Activity::getDropdownList(), ['prompt'=>'', 'class' => 'form-select']) ?>

        <?= $form->field($model, 'comment')->textInput(['maxlength' => true]) ?>

        <div class="form-group">
            <?= Html::submitButton(Module::t('Save'), ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>
</div>
