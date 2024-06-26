<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\RoleActivity $model */

$this->title = 'Update Role Activity: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Role Activities', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="role-activity-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
