<?php

use yii\helpers\Html;
use ZakharovAndrew\TimeTracker\Module;

/** @var yii\web\View $this */
/** @var ZakharovAndrew\TimeTracker\models\Activity $model */

$this->title = Module::t('Update Activity'). ': ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => Module::t('Activities'), 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="activity-update">

    <?php if (Yii::$app->getModule('timetracker')->showTitle) {?><h1><?= Html::encode($this->title) ?></h1><?php } ?>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
