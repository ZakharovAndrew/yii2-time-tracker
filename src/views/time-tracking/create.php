<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\TimeTracking $model */

$this->title = 'Create Time Tracking';
$this->params['breadcrumbs'][] = ['label' => 'Time Trackings', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="time-tracking-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
