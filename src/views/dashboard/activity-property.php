<?php

use yii\helpers\Html;
use ZakharovAndrew\TimeTracker\Module;
use ZakharovAndrew\TimeTracker\models\Activity;
use yii\helpers\Url;

/** @var yii\web\View $this */

$this->title = Module::t('Activity').($period == 'month' ? ' за месяц' : ($period == 'week' ? ' за неделю' : ' за день'));

//$this->title = Module::t('Update Activity Property') . ': ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => Module::t('Dashboard'), 'url' => ['index']];
//$this->params['breadcrumbs'][] = 'Update';

$script = <<< JS
    let filterTimeout;
    
    $('#filter').on('input', function() {        
        const searchText = $(this).val().toLowerCase().trim();
        
        if (!searchText) {
            $('#dashboard-detail tbody tr').show();
            return;
        }
        
        const searchWords = searchText.split(/\s+/); // Разделяем по пробелам
        
        $('#dashboard-detail tbody tr').each(function() {
            const \$item = $(this);
            const itemText = \$item.text().toLowerCase();
            
            // Проверяем, содержит ли элемент ВСЕ слова
            const containsAllWords = searchWords.every(word => 
                itemText.includes(word)
            );
            
            \$item.toggle(containsAllWords);
        });
    });
        
    $("#period").on('change', function() {
        document.location = '/timetracker/dashboard/activity-property?period='+$(this).val()+"&order_by=$order_by";
    });
JS;

$this->registerJs($script, yii\web\View::POS_READY);
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
  
    <p>
        Сортировать по: 
        <a href="<?= Url::to(['activity-property', 'period' => $period, 'order_by' => $order_by, 'order_by' => 'count']) ?>" class="<?= $order_by == 'count' ? 'active' : '' ?>"><?= Module::t('Count') ?></a> | 
        <a href="<?= Url::to(['activity-property', 'period' => $period, 'order_by' => $order_by, 'order_by' => 'duration']) ?>" class="<?= $order_by == 'duration' ? 'active' : '' ?>"><?= Module::t('Duration') ?></a>
        <b>Период</b> 
        <?= Html::dropDownList(
            'period',
            $period,
            ['' => 'день', 'week' => 'неделю', 'month' => 'месяц'],
            ['id' => 'period']
        );?>
        
        <input type="text" id="filter" placeholder="Фильтр по таблице..." class="form-control pull-right" style="max-width: 220px;padding: 4px 4px;height: 24px;">
    </p>
    
    <table class="table table-bordered" id="dashboard-detail">
        <thead>
            <tr>
                <th><?= Module::t('Activity') ?></th>
                <th><?= Module::t('Values') ?></th>
                <th><?= Module::t('Count') ?></th>
                <th><?= Module::t('Duration') ?></th>
                <th><?= Module::t('Average duration') ?></th>
            </tr>
        </thead>
            
    
    <?php foreach ($data as $item) {?>
        <tr>
            <td><?= $item['name'] ?></td>
            <td><?= $item['values'] ?></td>
            <td><?= $item['cnt'] ?></td>
            <td><?= isset($item['duration']) ? Activity::timeFormat($item['duration']) : 'N/A' ?></td>
            <td><?= isset($item['duration']) ? Activity::timeFormat(round($item['duration']/$item['cnt'])) : 'N/A'  ?></td>
        </tr>
    <?php } ?>
    </table>
</div>
