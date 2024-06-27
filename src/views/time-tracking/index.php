<?php

use ZakharovAndrew\TimeTracker\Module;
use ZakharovAndrew\TimeTracker\models\TimeTracking;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var ZakharovAndrew\TimeTracker\models\TimeTrackingSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = Module::t('Time Tracking');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="time-tracking-index">

    <?php if (Yii::$app->getModule('timetracker')->showTitle) {?><h1><?= Html::encode($this->title) ?></h1><?php } ?>

    <?php if ($user_activity !== null || count($user_activity) == 0) {?>
    <p>
        <?= Html::a(Module::t('Start the working day'), ['start'], ['class' => 'btn btn-success']) ?>
    </p>
    <?php } else { ?>
    <p>
        <?= Html::a('Add activity', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    
    <div class="card">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th><?= Module::t('Time') ?></th>
                    <th><?= Module::t('Activity') ?></th>
                    <th><?= Module::t('Comment') ?></th>
                </tr>
            </thead>
            <?php foreach ($user_activity as $item) {?>
            <tr>
                <td><?= date('H:i', strtotime($item->datetime_at))?></td>
                <td><?= $item->activity_id ?></td>
                <td><?= $item->comment ?></td>
            </tr>
            <?php } ?>
        </table>
    </div>
    
    <?php } ?>

</div>
