<?php

use ZakharovAndrew\TimeTracker\Module;
use ZakharovAndrew\TimeTracker\models\TimeTracking;
use ZakharovAndrew\TimeTracker\models\Activity;
use yii\helpers\Html;
use yii\bootstrap\Modal;
use yii\helpers\Url;

$bootstrapVersion = Yii::$app->getModule('timetracker')->bootstrapVersion;
$classModal = "\\yii\bootstrap".($bootstrapVersion==3 ? '' : $bootstrapVersion)."\\Modal";

/** @var yii\web\View $this */
/** @var ZakharovAndrew\TimeTracker\models\TimeTrackingSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = Module::t('Time Tracking');
$this->params['breadcrumbs'][] = $this->title;

$last_activity = end($user_activity);
?>
<link
    rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"
  />
<style>
    .time-tracking-box {
        box-shadow: 0px 2px 6px rgba(37, 83, 185, 0.1);
        border-radius: 15px;
        padding:15px;
        background: #fff;
    }
    .time-tracking-box .table {
        margin-bottom: 0;
    }
    .text-muted {
        color: rgba(112, 122, 130, 0.75);
    }
</style>
<div class="time-tracking-index">

    <?php if (Yii::$app->getModule('timetracker')->showTitle) {?><h1><?= Html::encode($this->title) ?></h1><?php } ?>

    <p>
    <?php if (!$user_activity || (is_array($user_activity) && count($user_activity) == 0)) {?>
    
        <?= Html::a(Module::t('Start the working day'), ['start'], ['class' => 'btn btn-success']) ?>
    
    <?php } else if ($last_activity->activity_id != Activity::STOP_DAY) { ?>

        <button type="button" class="btn btn-success" data-toggle="modal" data-target="#w0"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-plus" viewBox="0 0 16 16">
  <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4"></path>
</svg> <?= Module::t('Add Activity') ?></button>
        <?= Html::a(Module::t('Finish the working day'), ['stop'], ['class' => 'btn btn-danger']) ?>
    <?php } ?>
        
        <?php if ($allow_statistics) {?>
        <?= Html::a(Module::t('Statistics'), ['statistics'], ['class' => 'btn btn-info']) ?>
        <?php } ?>
        <?= Html::a(Module::t('My Statistics'), ['user-statistics'], ['class' => 'btn btn-info']) ?>
    </p>
    
    <?php if ($last_activity !== false && $last_activity->activity_id == Activity::STOP_DAY) { ?>
    <div class="alert alert-info"><?= Module::t('You have completed your working day.') ?></div>
    <?php if (Yii::$app->user->identity->hasRole('admin')) {
            echo Html::a(Module::t('Statistics'), ['statistics'], ['class' => 'btn btn-info']);
        }?>
    <?php } ?>
    
    <?php if ($user_activity) { ?>
    <div class="time-tracking-box animate__animated animate__fast animate__fadeInUp">
        <table class="table">
            <thead>
                <tr>
                    <th><?= Module::t('Time') ?></th>
                    <th><?= Module::t('Activity') ?></th>
                    <th><?= Module::t('Comment') ?></th>
                </tr>
            </thead>
            <?php 
            foreach ($user_activity as $item) {?>
            <tr>
                <td><div class="text-muted"><?= date('H:i', strtotime($item->datetime_at))?></div></td>
                <td><?= Activity::getList()[$item->activity_id] ?? $item->activity_id ?></td>
                <td><?= $item->comment ?> <?php
                    if ($item->id == $last_activity->id && $item->activity_id != Activity::START_DAY && $item->activity_id != Activity::STOP_DAY) {
                         echo Html::a(Module::t('Edit'), ['edit-comment'], ['class' => '']);   
                    }
                    ?>
                </td>
            </tr>
            <?php } ?>
        </table>
    </div>
    
    <?php } ?>

</div>

<?php
$classModal::begin([
    ($bootstrapVersion==3 ? 'header' : 'title') => '<h2>'.Module::t('Add Activity').'</h2>',
]);

echo $this->render('_form_add', ['model' => new TimeTracking()]);

$classModal::end();
