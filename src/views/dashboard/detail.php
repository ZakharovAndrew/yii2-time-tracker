<?php

use yii\helpers\Html;
use ZakharovAndrew\TimeTracker\Module;
use ZakharovAndrew\TimeTracker\models\Activity;
use yii\helpers\Url;

/** @var yii\web\View $this */

$this->title = Module::t('Activity').': '.$model->name;

//$this->title = Module::t('Update Activity Property') . ': ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => Module::t('Dashboard'), 'url' => ['index']];
//$this->params['breadcrumbs'][] = 'Update';
?>
<style>
    .dashboard-index .info-block h4 {
        margin-top: 0;
    }
</style>
<div class="dashboard-index">

    <?php if (Yii::$app->getModule('timetracker')->showTitle) {?><h1><?= Html::encode($this->title) ?></h1><?php } ?>
  
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th><?= Module::t('Name') ?></th>
                <th><?= Module::t('Count') ?></th>
                <th><?= Module::t('Duration') ?></th>
                <th><?= Module::t('Average duration') ?></th>
            </tr>
        </thead>
            
    
    <?php foreach ($data as $item) {?>
        <tr>
            <td><a href="<?= Url::to(['/user/user/profile', 'id'=> $item['user_id']]) ?>"><?= $item['user_id'] ?></a></td>
            <td><?= $item['name'] ?></td>
            <td><?= $item['cnt'] ?></td>
            <td><?= isset($item['duration']) ? Activity::timeFormat($item['duration']) : 'N/A' ?></td>
            <td><?= isset($item['duration']) ? Activity::timeFormat(round($item['duration']/$item['cnt'])) : 'N/A'  ?></td>
        </tr>
    <?php } ?>
    </table>
</div>
