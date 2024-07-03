<?php

use yii\helpers\Html;
use ZakharovAndrew\TimeTracker\Module;

/** @var yii\web\View $this */
/** @var app\models\RoleActivity $model */

$this->title = Module::t('Update Role Activity'). ': ' . $role->title;
$this->params['breadcrumbs'][] = ['label' => Module::t('Role Activities'). ' '. $role->title, 'url' => ['activities', 'role_id' => $role->id]];
$this->params['breadcrumbs'][] = Module::t('Update');
?>
<div class="role-activity-update">

    <?php if (Yii::$app->getModule('timetracker')->showTitle) {?><h1><?= Html::encode($this->title) ?></h1><?php } ?>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
