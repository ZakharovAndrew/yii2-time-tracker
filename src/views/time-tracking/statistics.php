<?php

use ZakharovAndrew\TimeTracker\Module;
use ZakharovAndrew\TimeTracker\models\Activity;
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
JS;
$this->registerJs($script, yii\web\View::POS_READY);
?>
<link
    rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"
  />
<?= $this->render('_timeline_style') ?>
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
        padding: 6px 7px;
        color: brown;
    }
    tbody tr:hover {
        background-color: #e0f3ff;
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
                <td>
                    <?= Html::a($user_name, ['user-statistics', 'user_id' => $user_id], ['class' => '']) ?>
                </td>
                <?php foreach ($timeline as $day => $item) {
                    /*$item_name = date('Y-m-d', strtotime($item[$user_id]->datetime_at));*/?>
                    <?php if ($item[$user_id] ?? '') {
                        $start = '';
                        $stop = '';
                        
                        foreach ($item[$user_id] as $activity) {
                            if ($activity->activity_id == Activity::START_DAY) {
                                $start = date('H:i', strtotime($activity->datetime_at));
                            }
                            if ($activity->activity_id == Activity::STOP_DAY) {
                                $stop = date('H:i', strtotime($activity->datetime_at));
                            }
                            $activity_list[$i][] = [
                                'activity' => $activities[$activity->activity_id],
                                'time' => date('H:i:s', strtotime($activity->datetime_at)),
                                'date' => date('d.m.Y', strtotime($activity->datetime_at)),
                                'comment' => str_replace('"', '\"', $activity->comment)//,
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
                        
                        echo '<td class="'.$class.'" data-id="'.$i.'" data-toggle="popover" data-user="'.$user_name.'" data-day="' . date('d.m.Y', strtotime($day)) .'"><span>'.$start.($stop ? ' - '.$stop : '').'</span></td>';
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

<script>
    let json ='<?= json_encode($activity_list, JSON_UNESCAPED_UNICODE)?>';
    let data = JSON.parse(json);
    
    function getActivityHtml(activity) {
        return `<div class="timeline-element">
                    <div>
                        <span class="timeline-icon">
                            <i class="badge badge-dot badge-dot-xl badge-warning"> </i>
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
