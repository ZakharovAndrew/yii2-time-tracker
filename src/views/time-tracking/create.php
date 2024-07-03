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

    <?= $this->render('_form_add', [
        'model' => $model,
    ]) ?>

</div>
