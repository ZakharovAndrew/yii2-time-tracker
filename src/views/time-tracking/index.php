<?php

use ZakharovAndrew\TimeTracker\Module;
use ZakharovAndrew\TimeTracker\models\TimeTracking;
use ZakharovAndrew\TimeTracker\models\Activity;
use ZakharovAndrew\TimeTracker\models\ActivityProperty;
use yii\helpers\Html;
use ZakharovAndrew\TimeTracker\assets\TimeTrackerAssets;

TimeTrackerAssets::register($this);

$bootstrapVersion = Yii::$app->getModule('timetracker')->bootstrapVersion;
$classModal = "\\yii\bootstrap".($bootstrapVersion==3 ? '' : $bootstrapVersion)."\\Modal";
$classTabs = "\\yii\bootstrap".($bootstrapVersion==3 ? '' : $bootstrapVersion)."\\Tabs";

$additionalPropertiesInWorkStatuses = Yii::$app->getModule('timetracker')->additionalPropertiesInWorkStatuses;
$additionalProperties = ActivityProperty::find()->where(['id' => $additionalPropertiesInWorkStatuses])->all();

/** @var yii\web\View $this */
/** @var ZakharovAndrew\TimeTracker\models\TimeTrackingSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = Module::t('Time Tracking');
$this->params['breadcrumbs'][] = $this->title;

$last_activity = end($user_activity);

$comment_list = Activity::getTemplateCommentsActivityByUserId(Yii::$app->user->id);
$hints = str_replace(['"', "\n", "\r"], ['\"',"<br>", ''], trim(implode('<br>', Activity::getHintsActivityByUserId(Yii::$app->user->id))));
$hint = Module::t('Hints');
  
$script = <<< JS
    $('.field-timetracking-activity_id').append('<div class="info-icon" data-toggle="popover" data-bs-placement="left" data-placement="left" title="$hint" data-content="$hints"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 50 50"><path style="fill:#fff;fill-opacity:1;stroke:none" d="M0 0h50v50H0z"/><path style="stroke:none;fill-rule:nonzero;fill:#000;fill-opacity:1" d="M48.785 25c0 13.137-10.648 23.785-23.785 23.785S1.215 38.137 1.215 25 11.863 1.215 25 1.215 48.785 11.863 48.785 25zm0 0"/><path style="stroke:none;fill-rule:nonzero;fill:#fff;fill-opacity:1" d="M25 42.2c-2.21 0-4-1.368-4-3.055V21.883c0-1.684 1.79-3.051 4-3.051s4 1.367 4 3.05v17.263c0 1.687-1.79 3.054-4 3.054zM28.938 12.426a3.939 3.939 0 1 1-7.878-.002 3.939 3.939 0 0 1 7.878.002zm0 0"/></svg></div>');   
    let counter;
    $('[data-toggle="popover"]').popover({
        html: true,
        trigger: 'manual',
        container: 'body'
    }).on("mouseenter", function(e) {
        var _this = this;
        e.preventDefault();
        clearTimeout(counter);
        $('[rel="popover"]').not(_this).popover('hide');
        counter = setTimeout(function(){
            if($(_this).is(':hover'))
            {
                $(_this).popover("show");
            }
            $(".popover").on("mouseleave", function () {
                $(_this).popover('hide');
            });
        }, 400);
        
    }).on("mouseleave", function () {
        var _this = this;

        setTimeout(function () {
            if (!$(".popover:hover").length) {
                if (!$(_this).is(':hover')) {
                   $(_this).popover('hide');
                }
            }
        }, 200);
    }); 
        

    console.log('start');
    
JS;

$this->registerJs($script, yii\web\View::POS_READY);
?>
<style>

    textarea.form-control {
        max-width:100%
    }
    .popover {
        max-width: 500px;
        z-index: 2000;
    }
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
    .time-tracking__time {
        width:75px;
    }
    .time-tracking__activity {
        text-wrap: nowrap;
    }
    .time-tracking__comment {
        width:100%;
    } 
    .field-timetracking-comment, .field-timetracking-activity_id {
        position:relative;
    }
    .field-timetracking-comment .copyng, .field-timetracking-activity_id .info-icon{
        position:absolute;
        top: -3px;
        right: 0;
    }
    
.comment-templates {max-height: 80vh; height:max; overflow-y:auto;}
.comment-templates-item {
    display: block;
    padding: 8px 6px;
    font-size:13px;
    line-height: 13px;

    border-bottom: 1px dotted #cdcdcd;
    cursor:pointer;
}
.comment-templates-item:hover {
    background-color: #ebebeb;
}

.comment-templates-item:last-child {
    border-bottom: 0 none;
}
.comment-templates-search {
    padding-bottom:6px;
}
.dropdown-menu-card {
    padding:5px
}


</style>
<div class="time-tracking-index">

    <?php if (Yii::$app->getModule('timetracker')->showTitle) {?><h1><?= Html::encode($this->title) ?></h1><?php } ?>

    <p>
    <?php if (!$user_activity || (is_array($user_activity) && count($user_activity) == 0) || $last_activity->activity_id == Activity::WORK_STOP) {?>
    
        <?= Html::a(Module::t('Start the working day'), ['start'], ['class' => 'btn btn-success']) ?>
    
    <?php } else if ($last_activity->activity_id != Activity::WORK_STOP) { ?>

        <button type="button" class="btn btn-success" data-toggle="modal" data-bs-toggle="modal" data-target="#time-tracking-add-activity" data-bs-target="#time-tracking-add-activity"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-plus" viewBox="0 0 16 16">
  <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4"></path>
</svg> <?= Module::t('Add Activity') ?></button>
        <?= Html::a(Module::t('Break'), ['break'], ['class' => 'btn btn-warning']) ?>
        <?= Html::a(Module::t('Finish the working day'), ['stop'], ['class' => 'btn btn-danger']) ?>
    <?php } ?>
        
        <?php 
        if ($allow_statistics) {
            echo Html::a(Module::t('Statistics'), ['statistics'], ['class' => 'btn btn-info']);
        } ?>
        <?= Html::a(Module::t('My Statistics'), ['user-statistics'], ['class' => 'btn btn-info']) ?>
        
        <span class="alert alert-warning" style="float: right; padding: 7px;">
        <?= Module::t('Break') . ' ' . Activity::timeFormat($breakTime) ?>
        </span>
        <span class="alert alert-info" style="float: right; padding: 7px; margin-right: 7px">
        <?= Module::t('Working hours') . ' ' .  Activity::timeFormat($workTime) ?>
        </span>
    </p>
    
    <?php if ($last_activity !== false && $last_activity->activity_id == Activity::WORK_STOP) { ?>
    <div class="alert alert-info"><?= Module::t('You have completed your working day.') ?></div>
    <?php } ?>
    
    <?php if ($user_activity) { ?>
    <div class="row" style="width:100%">
        <div class="col-md-6">
            <div class="time-tracking-box">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th class="time-tracking__time"><?= Module::t('Time') ?></th>
                                <th><?= Module::t('Activity') ?></th>
                                <th class="time-tracking__comment"><?= Module::t('Comment') ?></th>
                                <?php foreach ($additionalProperties as $prop) { ?>
                                <th><?= $prop->name ?></th>
                                <?php } ?>
                            </tr>
                        </thead>
                        <?php 
                        foreach ($user_activity as $item) { ?>
                        <tr>
                            <td><div class="text-muted"><?= date('H:i', strtotime($item->datetime_at))?></div></td>
                            <td class="time-tracking__activity"><?= Activity::getList()[$item->activity_id] ?? $item->activity_id ?></td>
                            <td><?= $item->comment ?> <?php
                                if ($item->id == $last_activity->id && $item->activity_id != Activity::WORK_START && $item->activity_id != Activity::WORK_STOP) {
                                     echo Html::a(Module::t('Edit'), ['edit-comment'], ['class' => '']);   
                                }
                                ?>
                            </td>
                            <?php foreach ($additionalProperties as $prop) { ?>
                            <td><?= $prop->getUserPropertyValue($item->id) ?></td>
                            <?php } ?>
                        </tr>
                        <?php } ?>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="time-tracking-box">
                <?php
                echo $classTabs::widget([
                    'items' => [
                        [
                            'label' => Module::t('Chart'),
                            'content' => '<canvas id="bar" width="800" height="450"></canvas>',
                            'active' => true
                        ],
                        [
                            'label' => Module::t('Table'),
                            'content' => $this->render('_table_activity', ['aggActivity' => $aggActivity]),
                        ],

                    ],
                ]);
                ?>
                
            </div>
        </div>
    </div>
    
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    
        
    function formatTime(seconds) {
        let hours = Math.floor(seconds / 3600);
        let minutes = Math.floor((seconds % 3600) / 60);
        //let second = Math.floor((seconds % (3600*60)/100);
        return hours + ':' + (minutes < 10 ? '0' : '') + minutes;
    }
    
    let sumActivities = <?= array_sum( array_values ($aggActivity)) ?>;
    let chartBar = new Chart(document.getElementById("bar"), {
        type: 'bar',
        
        data: {
            labels: <?= json_encode($labels) ?>,
            datasets: [{
                label: '<?= Module::t('Activity') ?>',
                backgroundColor: <?= json_encode($colors) ?>,
                data: <?= json_encode( array_values ($aggActivity)) ?>
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            scales: {
                x: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 60*30,
                        callback: formatTime
                    }
                }
            },
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(tooltipItem) {
                            let hours = Math.floor(tooltipItem.raw / 3600);
                            let minutes = Math.floor((tooltipItem.raw % 3600) / 60);

                            let timeLabel = hours + 'h ' + (minutes < 10 ? '0' : '') + minutes + 'm'
                            let activityProcent = (tooltipItem.raw / sumActivities) * 100;
                            return timeLabel + ' ' + Math.round(activityProcent, 2) + '%';
                        }
                    }
                }
            }
            
        }
    });
</script>
    </div>
    <?php } ?>

</div>

<?php
$classModal::begin([
    ($bootstrapVersion==3 ? 'header' : 'title') => '<h2>'.Module::t('Add Activity').'</h2>',
    'id' => 'time-tracking-add-activity'
]);

echo $this->render('_form_add', [
        'model' => new TimeTracking(),
        'properties' => \ZakharovAndrew\TimeTracker\models\ActivityProperty::find()->orderBy('pos ASC')->all()
    ]);

$classModal::end();
$this->registerJs("$.fn.modal.Constructor.prototype.enforceFocus = $.noop;");
?>

<div class="copying" id='comment-menu'>
    <div class="dropdown">
        <button class="btn dropdown-toggle" type="button" data-toggle="dropdown" ><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path fill="currentColor" d="M2 5.25A3.25 3.25 0 0 1 5.25 2h10.5A3.25 3.25 0 0 1 19 5.25V9H6.75a.75.75 0 0 0 0 1.5h3A3.73 3.73 0 0 0 9 12.75v6.5q0 .386.075.75H5.25A3.25 3.25 0 0 1 2 16.75zm4 1.5c0 .414.336.75.75.75h7.5a.75.75 0 0 0 0-1.5h-7.5a.75.75 0 0 0-.75.75m4 6A2.75 2.75 0 0 1 12.75 10h6.5A2.75 2.75 0 0 1 22 12.75v.75H10zM10 15h12v4.25A2.75 2.75 0 0 1 19.25 22h-6.5A2.75 2.75 0 0 1 10 19.25z"/></svg></button>
        <ul class="dropdown-menu dropdown-menu-left dropdown-menu-card">
          <div class="search-box">Искать <input type="text" id="comment-templates-filter"></div>
            <?php foreach ($comment_list as $key => $comment) {?>
            <div class="comment-templates comment-templates-<?= $key ?>">
                <?php foreach (explode("\n", $comment) as $item) {?>
                <li class="comment-templates-item dropdown-item"><?= $item ?></li>
                <?php } ?>
            </div>
            <?php } ?>
        </ul>
    </div>
</div>

<?php if (\Yii::$app->session->get('add_activity') == '1') {
$script = <<< JS
$("#time-tracking-add-activity").modal('show');
JS;

$this->registerJs($script, yii\web\View::POS_READY); 
\Yii::$app->session->remove('add_activity');
} ?>
