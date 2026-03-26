<?php

use yii\helpers\Html;
use ZakharovAndrew\TimeTracker\Module;
use ZakharovAndrew\TimeTracker\models\Activity;
use yii\helpers\Url;

/** @var yii\web\View $this */

$this->title = Module::t('Activity').($period == 'month' ? ' за месяц' : ''). ': '.$model->name;

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
        <a href="<?= Url::to(['detail', 'period' => $period, 'activity_id' => $activity_id, 'order_by' => $order_by, 'order_by' => 'count']) ?>" class="<?= $order_by == 'count' ? 'active' : '' ?>"><?= Module::t('Count') ?></a> | 
        <a href="<?= Url::to(['detail', 'period' => $period, 'activity_id' => $activity_id, 'order_by' => $order_by, 'order_by' => 'duration']) ?>" class="<?= $order_by == 'duration' ? 'active' : '' ?>"><?= Module::t('Duration') ?></a>
        
        <input type="text" id="filter" placeholder="Фильтр по таблице..." class="form-control pull-right" style="max-width: 220px;padding: 4px 4px;height: 24px;">
    </p>
    
    <table class="table table-bordered" id="dashboard-detail">
        <thead>
            <tr>
                <th>ID</th>
                <th><?= Module::t('Name') ?></th>
                <?php foreach ($user_properties_column as $property) {?>
                <th><?= $property->title ?></th>
                <?php } ?>
                <th><?= Module::t('Count') ?></th>
                <th><?= Module::t('Duration') ?></th>
                <th><?= Module::t('Average duration') ?></th>
            </tr>
        </thead>
            
    
    <?php foreach ($data as $item) {?>
        <tr>
            <td><a href="<?= Url::to(['/user/user/profile', 'id'=> $item['user_id']]) ?>"><?= $item['user_id'] ?></a></td>
            <td><?= $item['name'] ?></td>
            <?php foreach ($user_properties_column as $property) {?>
            <td><?= $property->getUserSettingValue($item['user_id']) ?></td>
            <?php } ?>
            <td><?= $item['cnt'] ?></td>
            <td><?= isset($item['duration']) ? Activity::timeFormat($item['duration']) : 'N/A' ?></td>
            <td><?= isset($item['duration']) ? Activity::timeFormat(round($item['duration']/$item['cnt'])) : 'N/A'  ?></td>
        </tr>
    <?php } ?>
    </table>
</div>
