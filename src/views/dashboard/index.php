<?php

use yii\helpers\Html;
use ZakharovAndrew\TimeTracker\Module;
use ZakharovAndrew\TimeTracker\models\Activity;

/** @var yii\web\View $this */

$this->title = Module::t('Dashboard');
?>
<style>
    .dashboard-index .info-block h4 {
        margin-top: 0;
    }
</style>
<div class="dashboard-index">

    <?php if (Yii::$app->getModule('timetracker')->showTitle) {?><h1><?= Html::encode($this->title) ?></h1><?php } ?>

    <h3>Количество активностей</h3>
    <div class="row">
        <div class="col-md-4">
            <div class="card info-block">
                <div class="card-body">
                    <h4>Всего</h4>
                    <span><?= $data['count_all_activities'] ?></span>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card info-block">
                <div class="card-body">
                    <h4>Последние месяц</h4>
                    <span><?= $data['coun_last_month_activities'] ?></span>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card info-block">
                <div class="card-body">
                    <h4>Последние 7 дней</h4>
                    <span><?= $data['coun_last_week_activities'] ?></span>
                </div>
            </div>
        </div>
    </div>
    
    <h3>Топ 10 активностей за месяц</h3>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th><?= Module::t('Activity') ?></th>
                <th>Count</th>
                <th><?= Module::t('Duration') ?></th>
            </tr>
        </thead>
            
    
    <?php foreach ($data['top_month_activities'] as $item) {?>
        <tr>
            <td><?= $item['name'] ?></td>
            <td><?= $item['cnt'] ?></td>
            <td><?= Activity::timeFormat($item['duration']) ?></td>
        </tr>
    <?php } ?>
    </table>
    
    <h3>Топ 10 активностей за неделю</h3>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th><?= Module::t('Activity') ?></th>
                <th>Count</th>
                <th><?= Module::t('Duration') ?></th>
            </tr>
        </thead>
            
    
    <?php foreach ($data['top_week_activities'] as $item) {?>
        <tr>
            <td><?= $item['name'] ?></td>
            <td><?= $item['cnt'] ?></td>
            <td><?= Activity::timeFormat($item['duration']) ?></td>
        </tr>
    <?php } ?>
    </table>
    
    <h3>Топ 10 активностей за сегодня</h3>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th><?= Module::t('Activity') ?></th>
                <th>Count</th>
            </tr>
        </thead>
            
    
    <?php foreach ($data['top_today_activities'] as $item) {?>
        <tr>
            <td><?= $item['name'] ?></td>
            <td><?= $item['cnt'] ?></td>
        </tr>
    <?php } ?>
    </table>
</div>
