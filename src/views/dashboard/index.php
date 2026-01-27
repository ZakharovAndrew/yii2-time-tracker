<?php

use yii\helpers\Html;
use ZakharovAndrew\TimeTracker\Module;
use ZakharovAndrew\TimeTracker\models\Activity;
use yii\helpers\Url;

/** @var yii\web\View $this */

$this->title = Module::t('Dashboard');
?>
<style>
    .row-dashboard .info-block div {
        margin-top: 0;
    }
    .row-dashboard span {
        font-weight:bold;
        font-size:36px;
        display:block;
    }
    .row-dashboard .card-body {
        text-align: center;
    }
    .timetracking-dashboard-index {
        font-family: Roboto;
    }
    .timetracking-dashboard-index table tr:hover td {background:#e2f6ff}
    .activity_name span {
        float:right;
    }
    .timetracking-dashboard-index .activity_name span .btn{padding:1px 5px;opacity:0;}
    .timetracking-dashboard-index table tr:hover .activity_name span .btn{opacity:1;}
</style>
<div class="timetracking-dashboard-index">

    <?php if (Yii::$app->getModule('timetracker')->showTitle) {?><h1><?= Html::encode($this->title) ?></h1><?php } ?>

    <h3>Количество активностей</h3>
    <div class="row row-dashboard">
        <div class="col-md-3">
            <div class="card info-block">
                <div class="card-body">
                    <div>Всего</div>
                    <span><?= $data['count_all_activities'] ?></span>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card info-block">
                <div class="card-body">
                    <div>Последний месяц</div>
                    <span><?= $data['count_last_month_activities'] ?></span>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card info-block">
                <div class="card-body">
                    <div>Последние 7 дней</div>
                    <span><?= $data['count_last_week_activities'] ?></span>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card info-block">
                <div class="card-body">
                    <div>Сегодня</div>
                    <span><?= $data['count_today_activities'] ?></span>
                </div>
            </div>
        </div>
    </div>
    
    <?php 
    $stats = [
        'top_month_activities' => [
            'title' => 'Топ 10 активностей за месяц',
            'period' => 'month'
        ],
        'top_week_activities' => [
            'title' => 'Топ 10 активностей за неделю',
            'period' => 'week'
        ],
        'top_today_activities' => [
            'title' => 'Топ 10 активностей за сегодня',
            'period' => 'today'
        ],
    ]
    ?>
    
    <?php foreach ($stats as $key => $stat) { ?>
    
    <h3><?= $stat['title'] ?></h3>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th><?= Module::t('Activity') ?></th>
                <th><?= Module::t('Count') ?></th>
                <th><?= Module::t('Users') ?></th>
                <th><?= Module::t('Duration') ?></th>
                <th><?= Module::t('Average duration') ?></th>
            </tr>
        </thead>
            
    
    <?php foreach ($data[$key] as $item) {?>
        <tr>
            <td class="activity_name">
                <a href="<?= Url::to(['detail', 'activity_id'=> $item['activity_id'], 'period' => $stat['period']]) ?>"><?= $item['name'] ?></a>
                <span><a href="<?= Url::to(['activity', 'id'=> $item['activity_id']]) ?>" class="btn btn-success" title="Статистика по активности за 6 месяцев"><b>S</b></a><span></span>
            </td>
            <td><?= $item['cnt'] ?></td>
            <td><?= $item['cnt_users'] ?></td>
            <td><?= Activity::timeFormat($item['duration']) ?></td>
            <td><?= Activity::timeFormat(round($item['duration']/$item['cnt'])) ?></td>
        </tr>
    <?php } ?>
    </table>
    <?php } ?>
    
    <h3>Топ 10 активностей за сегодня</h3>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th><?= Module::t('Activity') ?></th>
                <th><?= Module::t('Count') ?></th>
                <th><?= Module::t('Users') ?></th>
                <th><?= Module::t('Duration') ?></th>
                <th><?= Module::t('Average duration') ?></th>
            </tr>
        </thead>
            
    
    <?php foreach ($data['top_today_activities'] as $item) {?>
        <tr>
            <td class="activity_name">
                <a href="<?= Url::to(['detail', 'activity_id'=> $item['activity_id'], 'period' => 'today']) ?>"><?= $item['name'] ?></a>
                <span><a href="<?= Url::to(['activity', 'id'=> $item['activity_id']]) ?>" class="btn btn-success" title="Статистика по активности за 6 месяцев"><b>S</b></a><span></span>
            </td>
            <td><?= $item['cnt'] ?></td>
            <td><?= $item['cnt_users'] ?></td>
            <td><?= Activity::timeFormat($item['duration'] ?? 0) ?></td>
            <td><?= Activity::timeFormat(round($item['duration']/$item['cnt'])) ?></td>
            
        </tr>
    <?php } ?>
    </table>
</div>
