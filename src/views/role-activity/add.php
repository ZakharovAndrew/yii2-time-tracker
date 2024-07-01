<?php

use yii\helpers\Html;
use ZakharovAndrew\TimeTracker\Module;

/** @var yii\web\View $this */
/** @var ZakharovAndrew\TimeTracker\models\RoleActivity $model */

$this->title = Module::t('Add Activity for Role');
$this->params['breadcrumbs'][] = ['label' => Module::t('Role Activities'), 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Add';
?>
<div class="role-activity-update">

    <?php if (Yii::$app->getModule('timetracker')->showTitle) {?><h1><?= Html::encode($this->title) ?></h1><?php } ?>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
