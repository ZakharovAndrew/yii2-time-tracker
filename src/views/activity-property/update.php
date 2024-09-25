<?php

use yii\helpers\Html;
use ZakharovAndrew\TimeTracker\Module;

/** @var yii\web\View $this */
/** @var ZakharovAndrew\TimeTracker\models\ActivityProperty $model */

$this->title = Module::t('Update Activity Property') . ': ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => Module::t('Activity Properties'), 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="activity-property-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
