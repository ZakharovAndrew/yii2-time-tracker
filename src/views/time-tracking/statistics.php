<?php

use ZakharovAndrew\TimeTracker\Module;
use ZakharovAndrew\TimeTracker\models\TimeTracking;
use ZakharovAndrew\TimeTracker\models\Activity;
use yii\helpers\Html;
use yii\bootstrap\Modal;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var ZakharovAndrew\TimeTracker\models\TimeTrackingSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = Module::t('Statistics');
$this->params['breadcrumbs'][] = $this->title;

$str = Module::t('Activities');

$script = <<< JS
$('[data-toggle="popover"]').click(function() {
    let content = $(this).data('content');
    let day = $(this).data('day');
    $("#popover-modal-title").html('$str ' + day);
    $("#popover-modal .modal-body").html(content);
    $('#popover-modal').modal('show');
});
    

JS;
$this->registerJs($script, yii\web\View::POS_READY);
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
    [data-toggle="popover"], thead th {
        text-align:center;
    }
    thead th:first-child {
        text-align:left;
    }
    [data-toggle="popover"]:hover {
        background-color: #e6e6e6;
        border-color: #adadad;
        cursor:pointer
    }
</style>
<div class="time-tracking-index">

    <?php if (Yii::$app->getModule('timetracker')->showTitle) {?><h1><?= Html::encode($this->title) ?></h1><?php } ?>

    
    
    <?php if ($timeline) { ?>
    
    
    <div class="time-tracking-box animate__animated animate__fast animate__fadeInUp">
        <table class="table">
            <thead>
                <tr>
                    <th><?= Module::t('User') ?></th>
                    <?php foreach ($timeline as $day => $item) {?>
                    <th><?= date('d.m.Y', strtotime($day))  ?></th>
                    <?php } ?>
                    
                </tr>
            </thead>
            
            <?php foreach ($users as $user_id => $user_name) {?>
            <tr>
                <td><?= $user_name ?></td>
                <?php foreach ($timeline as $day => $item) {
                    /*$item_name = date('Y-m-d', strtotime($item[$user_id]->datetime_at));*/?>
                    <?php if ($item[$user_id] ?? '') {
                        $start = '';
                        $stop = '';
                        $popover = '';
                        foreach ($item[$user_id] as $activity) {
                            $popover .= date('H:i:s', strtotime($activity->datetime_at));
                            $popover .= ' '. $activities[$activity->activity_id].'<br>';
                            if ($activity->activity_id == Activity::START_DAY) {
                                $start = date('H:i', strtotime($activity->datetime_at));
                            }
                            if ($activity->activity_id == Activity::STOP_DAY) {
                                $stop = date('H:i', strtotime($activity->datetime_at));
                            }
                        }
                        echo '<td data-toggle="popover" data-day="' . date('d.m.Y', strtotime($day)) .'" data-trigger="focus" data-content="'.$popover.'">'.$start.($stop ? ' - '.$stop : '').'</td>';
                    } else {
                    ?>
                    <td></td>
                    <?php } ?>
                <?php } ?>
            </tr>
            <?php } ?>
        </table>
    </div>
    
    <?php } ?>

</div>

<?php
Modal::begin([
    'id' => 'popover-modal',
    'header' => '<h2 id="popover-modal-title">'.Module::t('Activities').'</h2>',
]);


Modal::end();
