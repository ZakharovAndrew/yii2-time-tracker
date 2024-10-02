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

    <?php if (Yii::$app->getModule('timetracker')->showTitle) {?><h1><?= Html::encode($this->title) ?></h1><?php } ?>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
