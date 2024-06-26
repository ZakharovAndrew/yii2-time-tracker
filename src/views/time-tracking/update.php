<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\TimeTracking $model */

$this->title = 'Update Time Tracking: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Time Trackings', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="time-tracking-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
