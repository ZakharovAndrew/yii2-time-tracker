<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use ZakharovAndrew\TimeTracker\Module;

/** @var yii\web\View $this */
/** @var ZakharovAndrew\TimeTracker\models\Activity $model */
/** @var yii\widgets\ActiveForm $form */
?>
<style>
    #activity-color {
        width: 33px;
        padding: 2px 3px;
        cursor:pointer;
    }
</style>
<div class="activity-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
    
    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'comment_templates')->textarea(['rows' => '6']) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'hint')->textarea(['rows' => '6']) ?>
        </div>
    </div>
    
    
    <?= $form->field($model, 'color')->input('color') ?>

    <div class="form-group">
        <?= Html::submitButton(Module::t('Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
