<?php

use yii\helpers\Html;
use ZakharovAndrew\TimeTracker\Module;

/** @var yii\web\View $this */
/** @var ZakharovAndrew\TimeTracker\models\TimeTracking $model */

$this->title = Module::t('Edit activity');
$this->params['breadcrumbs'][] = ['label' => 'Time Trackings', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="time-tracking-update">

    <?php if (Yii::$app->getModule('timetracker')->showTitle) {?><h1><?= Html::encode($this->title) ?></h1><?php } ?>

    <?= $this->render('_form', [
        'model' => $model,
        'activity' => $activity
    ]) ?>

</div>
