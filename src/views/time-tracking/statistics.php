<?php

use ZakharovAndrew\TimeTracker\Module;
use ZakharovAndrew\TimeTracker\models\Activity;
use yii\widgets\ActiveForm;
use yii\helpers\Html;

$bootstrapVersion = Yii::$app->getModule('timetracker')->bootstrapVersion;
$classModal = "\\yii\bootstrap".($bootstrapVersion==3 ? '' : $bootstrapVersion)."\\Modal";

/** @var yii\web\View $this */
/** @var ZakharovAndrew\TimeTracker\models\TimeTrackingSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = Module::t('Statistics');
$this->params['breadcrumbs'][] = ['label' => Module::t('Time Tracking'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$str = Module::t('Activities');

// for json
$activity_list = [];
$i = 1;

$script = <<< JS
        
$('[data-toggle="popover"]').click(function() {
    let id = $(this).data('id');
    let day = $(this).data('day');

    let content = '<div class="vertical-timeline">';
    data[id].forEach((activity) => content += getActivityHtml(activity));
    content += '</div>';
        
    // prepare and show modal
    $("#popover-modal .modal-body").html(content);
    $("#popover-modal-title").html('$str ' + $(this).data('user'));
    $('#popover-modal').modal('show');
});
$("h1").parent().append('<div id="filter" class="btn mg-top--10"><svg height="18" width="18" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 300.906 300.906" xml:space="preserve"><path d="M288.953 0h-277c-5.522 0-10 4.478-10 10v49.531c0 5.522 4.478 10 10 10h12.372l91.378 107.397v113.978a10 10 0 0 0 15.547 8.32l49.5-33a9.999 9.999 0 0 0 4.453-8.32v-80.978l91.378-107.397h12.372c5.522 0 10-4.478 10-10V10c0-5.522-4.477-10-10-10zM167.587 166.77a9.999 9.999 0 0 0-2.384 6.48v79.305l-29.5 19.666V173.25a9.997 9.997 0 0 0-2.384-6.48L50.585 69.531h199.736l-82.734 97.239zM278.953 49.531h-257V20h257v29.531z"/></svg></div>');
$("#filter").click(function() {
    if ($(".settings-modal").hasClass('show')) {
        $(".settings-modal").removeClass('show');
    } else {
        $(".settings-modal").addClass('show');
    }
});
        
$(".settings-modal .btn-modal-close").click(function() {
    $(this).parent().parent().removeClass('show');
});
        
JS;
$this->registerJs($script, yii\web\View::POS_READY);
?>
<link
    rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"
  />
<?= $this->render('_timeline_style') ?>
<style>
    h1 {display:inline-block}
    .mg-top--10 {margin-top: -10px;}
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
        background-color: #e6e6e6 !important;
        border-color: #adadad;
        cursor:pointer
    }
    .td-warning span {
        background-color: #fff8e8 !important;
        border-radius: 5px;
        padding: 6px 7px;
    }
    .td-danger span {
        background-color: #ffece8 !important;
        border-radius: 5px;
        padding: 4px 7px;
        color: brown;
        display: inline-block;
    }
    tbody tr:hover {
        background-color: #e0f3ff;
    }
    .last_activity {
        font-size: 10px;
        color: #cdcdcd;
        margin-top: 5px;
    }
    
    .nice-shadow {
        box-shadow: rgba(0, 0, 0, 0.05) -5px 10px 15px;
    }
    .modal.right.fade.in .modal-dialog {
right:0 !important;
transform: translateX(-50%);
}
.modal.right .modal-content {
height:100%;
overflow:auto;
border-radius:0;
border:none;
}
.modal.right .modal-dialog {
position: fixed;
margin: auto;
height: 100%;
-webkit-transform: translate3d(0%, 0, 0);
-ms-transform: translate3d(0%, 0, 0);
-o-transform: translate3d(0%, 0, 0);
transform: translate3d(0%, 0, 0);
}
.modal.right.fade.in .modal-dialog {
transform: translateX(0%);
}
.modal.right.fade .modal-dialog {
right: -320px;
-webkit-transition: opacity 0.3s linear, right 0.3s ease-out;
-moz-transition: opacity 0.3s linear, right 0.3s ease-out;
-o-transition: opacity 0.3s linear, right 0.3s ease-out;
transition: opacity 0.3s linear, right 0.3s ease-out;
}

.settings-modal .btn-modal-close {
    padding: 7px 8px 2px;
    background: #7ba3ff24;
    border-radius: 6px;
}
.settings-modal {
    transition: transform .5s cubic-bezier(.32, .72, 0, 1);
    transform: translate3d(130%, 0, 0);
    position: fixed;
    top: 0px;
    right: 0px;
    height: 100vh;
    overflow: hidden auto;
    z-index: 10;
    box-shadow: rgba(0, 0, 0, 0.05) -5px 10px 15px;
    background: #fff;
    padding: 10px 15px;
    z-index: 1050;
    min-width: 260px;
}
.settings-modal-title:first-child {
    font-size: 18px;
    font-weight: bold;
    line-height: 1.2;
}
.settings-modal-title {
    padding: 0 0 25px;
    display: flex;
    -webkit-box-pack: justify;
    justify-content: space-between;
    -webkit-box-align: center;
    align-items: center;
}
.settings-modal.show {
    transform: translate3d(0, 0, 0);
}
.settings-modal .btn-primary {
    color: #fff;
    background-color: #2196F3;
    border-color: #2196F3;
}
.settings-modal .btn-primary:hover {
    color: #fff;
    background-color: #42A5F5;
    border-color: #42A5F5;
}
.settings-modal .btn-primary:active {
    color: #fff;
    background-color: #1976D2;
    border-color: #1976D2;
}
.settings-modal .bottom-panel {
    position: absolute;
    display: flex;
    bottom: 0;
    width: 100%;
    left: 0;
    padding: 0 12px;
}
.settings-modal .bottom-panel button {
    width: 100%;
}
</style>
<div class="time-tracking-statistics">

    <?php if (Yii::$app->getModule('timetracker')->showTitle) {?><h1><?= Html::encode($this->title) ?></h1><?php } ?>

    
    
    <?php if ($timeline) { ?>
    
    
    <div class="time-tracking-box animate__animated animate__fast animate__fadeInUp">
        <table class="table">
            <thead>
                <tr>
                    <th class="time-tracking-user"><?= Module::t('User') ?></th>
                    <?php foreach ($timeline as $day => $item) {?>
                    <th><?= date('d.m.Y', strtotime($day))  ?></th>
                    <?php } ?>
                    
                </tr>
            </thead>
            
            <?php foreach ($users as $user_id => $user_name) {?>
            <tr>
                <td>
                    <?= Html::a($user_name, ['user-statistics', 'user_id' => $user_id], ['class' => '']) ?>
                </td>
                <?php foreach ($timeline as $day => $item) {
                    /*$item_name = date('Y-m-d', strtotime($item[$user_id]->datetime_at));*/?>
                    <?php if ($item[$user_id] ?? '') {
                        $start = '';
                        $stop = '';
                        
                        foreach ($item[$user_id] as $activity) {
                            if ($activity->activity_id == Activity::WORK_START) {
                                $start = date('H:i', strtotime($activity->datetime_at));
                            }
                            if ($activity->activity_id == Activity::WORK_STOP) {
                                $stop = date('H:i', strtotime($activity->datetime_at));
                            }
                                            
                            $activity_list[$i][] = [
                                'id' => $activity->activity_id,
                                'activity' => $activities[$activity->activity_id],
                                'time' => date('H:i:s', strtotime($activity->datetime_at)),
                                'date' => date('d.m.Y', strtotime($activity->datetime_at)),
                                'comment' => $activity->comment ?? ''
                            ];
                            
                        }
                        
                        $class = '';

                        //not current day
                        if (date('d.m.Y', strtotime($day)) != date('d.m.Y') || !empty($stop)) {
                            $time_diff = round((strtotime($stop ?? $start) - strtotime($start))/3600);
                            if ($time_diff < 6) {
                                $class = 'td-warning';
                            }
                            if (empty($stop)) {
                                $class = 'td-danger';
                                $alert = '⚠ ';
                            }  
                        }
                        
                        // last status
                        $end_activity =  end($item[$user_id]);
                        $hint = '';
                        if ($end_activity->activity_id != Activity::WORK_STOP && $end_activity->activity_id != Activity::WORK_START) {
                            $hint = '<div class="last_activity">'.$activities[$activity->activity_id].'</div>';
                        }
                        
                        echo '<td class="'.$class.'" data-id="'.$i.'" data-toggle="popover" data-user="'.$user_name.'" data-day="' . date('d.m.Y', strtotime($day)) .'"><span>'.$start.($stop ? ' - '.$stop : '').'</span>'.$hint.'</td>';
                        $i++;
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
$classModal::begin([
    'id' => 'popover-modal',
    ($bootstrapVersion==3 ? 'header' : 'title') => '<h2 id="popover-modal-title">'.Module::t('Activities').'</h2>',
]);

$classModal::end();
?>

<div class="settings-modal" data-modal-name="settings">
    <div class="settings-modal-title">
        <div>Фильтр</div>
        <div class="btn btn-modal-close">
            
<?xml version="1.0" encoding="UTF-8"?>
<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="14px" height="14px" viewBox="0 0 50 50" version="1.1">
<g id="surface1">
<path style=" stroke:none;fill-rule:nonzero;fill:rgb(33 150 243);fill-opacity:1;" d="M 2.722656 5.144531 L 5.152344 2.75 C 6.542969 1.328125 8.867188 1.328125 10.253906 2.75 L 25.003906 17.464844 L 39.753906 2.75 C 41.144531 1.328125 43.46875 1.328125 44.855469 2.75 L 47.25 5.144531 C 48.671875 6.53125 48.671875 8.859375 47.25 10.246094 L 32.535156 24.996094 L 47.25 39.746094 C 48.671875 41.132812 48.671875 43.457031 47.25 44.847656 L 44.855469 47.277344 C 43.46875 48.664062 41.144531 48.664062 39.753906 47.277344 L 25.003906 32.527344 L 10.253906 47.277344 C 8.867188 48.664062 6.542969 48.664062 5.152344 47.277344 L 2.722656 44.847656 C 1.335938 43.457031 1.335938 41.132812 2.722656 39.746094 L 17.472656 24.996094 L 2.722656 10.246094 C 1.335938 8.859375 1.335938 6.53125 2.722656 5.144531 Z M 2.722656 5.144531 "/>
</g>
</svg>
        </div>
    </div>
    
<?php $form = ActiveForm::begin([
        'action' => ['statistics'],
        'method' => 'get',
    ]); ?>

    <div class="form-group">
        <label>Дата с</label>
        <?= Html::input('date', 'datetime_start', $datetime_start ?? '', ['class' => 'form-control']) ?>
    </div>
    <div class="form-group">
        <label>Дата по</label>
        <?= Html::input('date', 'datetime_stop', $datetime_stop ?? '', ['class' => 'form-control']) ?>
    </div>

    <!-- <div class="form-group">
        <label>ФИО</label>
        <?= Html::input('input', 'username', $username ?? '', ['class' => 'form-control']) ?>
    </div> -->

    <div class="form-group bottom-panel">
        <?= Html::submitButton(Module::t('Apply'), ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>


<script>
    let data = <?= json_encode($activity_list, JSON_UNESCAPED_UNICODE) ?>;
    
    function getActivityHtml(activity) {
        return `<div class="timeline-element">
                    <div>
                        <span class="timeline-icon">
                            <i class="badge badge-dot badge-dot-xl badge-warning activity-${activity.id}"> </i>
                        </span>
                        <div class="timeline-content">
                            <h4 class="timeline-title">${activity.activity}</h4>
                            <p> ${activity.comment}</p>
                            <span class="timeline-date">${activity.time}</span>
                        </div>
                    </div>
                </div>`;
    }
</script>


