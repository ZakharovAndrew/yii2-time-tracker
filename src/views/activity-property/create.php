<?php

use yii\helpers\Html;
use ZakharovAndrew\TimeTracker\Module;

/** @var yii\web\View $this */
/** @var ZakharovAndrew\TimeTracker\models\ActivityProperty $model */

$this->title = Module::t('Create Activity Property');
$this->params['breadcrumbs'][] = ['label' => Module::t('Activity Properties'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="activity-property-create">

    <?php if (Yii::$app->getModule('timetracker')->showTitle) {?><h1><?= Html::encode($this->title) ?></h1><?php } ?>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
