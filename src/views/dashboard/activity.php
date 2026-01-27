<?php

use yii\helpers\Html;
use ZakharovAndrew\TimeTracker\Module;
use ZakharovAndrew\TimeTracker\models\Activity;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;

/** @var yii\web\View $this */

$this->title = Module::t('Activity').': '.$model->name;

$this->params['breadcrumbs'][] = ['label' => Module::t('Dashboard'), 'url' => ['index']];
?>

<style>
    .timetracking-dashboard-detail .info-block h4 {
        margin-top: 0;
    }
    .timetracking-dashboard-detail .active {
        font-weight: bold;
    }
    .timetracking-dashboard-detail table tr:hover td {background:#e2f6ff}
</style>
<div class="timetracking-dashboard-detail">

    <?php if (Yii::$app->getModule('timetracker')->showTitle) {?><h1><?= Html::encode($this->title) ?></h1><?php } ?>
  
    <div class="info-block">
        <div style="max-height:500px;position: relative;">
            <canvas id="dashboard_body" style="width: 100%; height: 600px!important"></canvas>
        </div>

        
    </div>

    <table class="table table-bordered" id="dashboard-detail">
        <thead>
            <tr>
                <th>Month</th>
                <th><?= Module::t('Count') ?></th>
                <th><?= Module::t('Duration') ?></th>
                <th><?= Module::t('Average duration') ?></th>
            </tr>
        </thead>
            
    <?php foreach ($data as $item) { ?>
        <tr>
            <td><?= Html::a($item['month_name'], ['detail', 'activity_id'=> $model->id, 'period' => 'month', 'date' => $item['month']]) ?></td>
            <td><?= $item['cnt'] ?></td>
            <td><?= isset($item['duration']) ? Activity::timeFormat($item['duration']) : 'N/A' ?></td>
            <td><?= isset($item['duration']) ? Activity::timeFormat(round($item['duration']/$item['cnt'])) : 'N/A'  ?></td>
        </tr>
    <?php } ?>
    </table>
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.5.0/Chart.min.js"></script>
    <script>
        const handleResize = (chart) => {
            chart.resize();
        }
        new Chart(document.getElementById("dashboard_body"), {
            type: 'bar',
            data: {
                labels: <?= json_encode(ArrayHelper::getColumn($data, 'month_name'), JSON_UNESCAPED_UNICODE) ?>,
                datasets: [
                    {
                        label: "Всего активностей",
                        backgroundColor: '#3cba9f',
                        data: <?= json_encode(ArrayHelper::getColumn($data, 'cnt'), JSON_UNESCAPED_UNICODE) ?>,
                    },
                ]
            },
            options: {
                responsive: true,
                onResize: handleResize,
                maintainAspectRatio: false,
                legend: { display: false },
                title: {
                    display: true,
                    text: 'Количество активностей по месяцам'
                }
            }
        });
    </script>
</div>
