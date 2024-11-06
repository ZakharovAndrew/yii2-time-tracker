<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use ZakharovAndrew\TimeTracker\models\Activity;
use ZakharovAndrew\TimeTracker\models\ActivityProperty;
use ZakharovAndrew\TimeTracker\Module;
use ZakharovAndrew\user\assets\UserAssets;
use ZakharovAndrew\user\models\UserSettingsConfig;

UserAssets::register($this);

/** @var yii\web\View $this */
/** @var ZakharovAndrew\TimeTracker\models\ActivityProperty $model */
/** @var yii\widgets\ActiveForm $form */
?>

<style>
    #activityproperty-params-show_when_activity label {width:24%}
    
</style>

<div class="activity-property-form white-block">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'type')->dropDownList(ActivityProperty::getTypeOfProperties(), ['prompt' => '', 'class' => 'form-control form-select']) ?>

    <?= $form->field($model, 'pos')->textInput() ?>

    <?= $form->field($model, 'values')->textarea(['rows' => 6]) ?>
    
    <?= $form->field($model, 'required')->dropDownList([ 0 => Module::t('No'), 1 => Module::t('Yes')], ['class' => 'form-control form-select']) ?>
    
    <h3><?= Module::t('Visibility conditions') ?></h3>
    <?php for($i = 1; $i <= 10; $i++) { ?>
    <div class="row" style="padding-bottom: 15px;">
        <div class="col-md-2">
            <?=$form->field($model, "params[user_property][$i][logic]")->dropDownList(['AND' => 'И', 'OR' => 'ИЛИ'], ['prompt' => '', 'class' => 'form-control form-select'])->label("Логика"); ?>
        </div>
        <div class="col-md-4">
            <?=$form->field($model, "params[user_property][$i][name]")->dropDownList(array_merge([
                    'email' => 'Email',
                    'username' => Module::t('Username'),
                    'sex' => Module::t('Sex'),
                    'phone' => Module::t('Phone'),
                ], \yii\helpers\ArrayHelper::map(UserSettingsConfig::find()->asArray()->all(), "code", "title")
                ), ['prompt' => '', 'class' => 'form-control form-select'])->label(Module::t('User Property')." # $i"); ?>
        </div>
        <div class="col-md-2">
            <?=$form->field($model, "params[user_property][$i][comparison]")->dropDownList(ActivityProperty::getComparisonList(), ['prompt' => '', 'class' => 'form-control form-select'])->label("Сравнение"); ?>
        </div>
        <div class="col-md-4">
            <?=$form->field($model, "params[user_property][$i][value]")->textInput()->label(Module::t('Value')); ?>
        </div>
    </div>
    <?php } ?>
    
    <h3><?= Module::t('Visibility conditions') ?> (<?= Module::t('Activity Properties') ?>)</h3>
    <?php $allow_activities = \yii\helpers\ArrayHelper::map(ActivityProperty::find()->where(['<>', 'id', $model->id ?? 0])->asArray()->all(), "id", "name");
    ?>
    <?php for($i = 1; $i <= 10; $i++) { ?>
    <div class="row" style="padding-bottom: 15px;">
        <div class="col-md-2">
            <?=$form->field($model, "params[activity_property][$i][logic]")->dropDownList(['AND' => 'И', 'OR' => 'ИЛИ'], ['prompt' => '', 'class' => 'form-control form-select'])->label("Логика"); ?>
        </div>
        <div class="col-md-4">
            <?=$form->field($model, "params[activity_property][$i][name]")->dropDownList($allow_activities
                , ['prompt' => '', 'class' => 'form-control form-select'])->label(Module::t('Activity Property'). " #$i"); ?>
        </div>
        <div class="col-md-2">
            <?=$form->field($model, "params[activity_property][$i][comparison]")->dropDownList(array_merge(ActivityProperty::getComparisonList(), ['checked' => 'checked']), ['prompt' => '', 'class' => 'form-control form-select'])->label("Сравнение"); ?>
        </div>
        <div class="col-md-4">
            <?=$form->field($model, "params[activity_property][$i][value]")->textInput()->label(Module::t('Value')); ?>
        </div>
    </div>
    <?php } ?>
    
    <h3><?= Module::t('Activities for which to display a property') ?></h3>
    
    <?= $form->field($model, 'params[show_when_activity]')->checkboxList(Activity::getDropdownList())->label(false); ?>

    <div class="form-group">
        <?= Html::submitButton(Module::t('Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
