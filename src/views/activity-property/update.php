<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var ZakharovAndrew\TimeTracker\models\ActivityProperty $model */

$this->title = 'Update Activity Property: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Activity Properties', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="activity-property-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
