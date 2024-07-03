<?php

use ZakharovAndrew\TimeTracker\Module;
use ZakharovAndrew\TimeTracker\models\TimeTracking;
use ZakharovAndrew\TimeTracker\models\Activity;
use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var ZakharovAndrew\TimeTracker\models\TimeTrackingSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Timeline';
$this->params['breadcrumbs'][] = $this->title;

?>
<style>
.vertical-timeline {
    width: 100%;
    position: relative;
    padding: 1.5rem 0 1rem;
}
.vertical-timeline-element {
    position: relative;
    margin: 0 0 1rem;
}
.vertical-timeline::before {
    content: '';
    position: absolute;
    top: 0;
    left: 67px;
    height: 100%;
    width: 4px;
    background: #e9ecef;
    border-radius: .25rem;
}
.vertical-timeline-element-content {
    position: relative;
    margin-left: 90px;
    font-size: 11px;
}
.vertical-timeline-element-content .timeline-title {
    font-size: 12px;
    text-transform: uppercase;
    margin: 0 0 .5rem;
    padding: 2px 0 0;
    font-weight: 700;
}
.vertical-timeline-element-content p {
    color: #6c757d;
    margin: 0 0 .5rem;
}
.vertical-timeline-element-content .vertical-timeline-element-date {
    display: block;
    position: absolute;
    left: -90px;
    top: 0;
    padding-right: 10px;
    text-align: right;
    color: #adb5bd;
    font-size: 12px;
    white-space: nowrap;
}
.vertical-timeline-element-content:after {
    content: "";
    display: table;
    clear: both;
}
.vertical-timeline-element:after {
    content: "";
    display: table;
    clear: both;
}
.vertical-timeline-element-icon {
    position: absolute;
    top: -4px;
    left: 60px;
}
.badge-dot-xl::before {
    content: '';
    width: 10px;
    height: 10px;
    border-radius: .25rem;
    position: absolute;
    left: 50%;
    top: 50%;
    margin: -5px 0 0 -5px;
    background: #fff;
}
.vertical-timeline-element-icon .badge-dot-xl {
    box-shadow: 0 0 0 5px #fff;
}



.badge-warning {
    color: #212529;
    background-color: #ae71ce;
}
.badge {
    font-weight: 700;
    text-transform: uppercase;
    padding: 5px 10px;
    min-width: 19px;
}

.badge-dot {
    text-indent: -999em;
    padding: 0;
    width: 8px;
    height: 8px;
    border: transparent solid 1px;
    border-radius: 30px;
    min-width: 2px;
}

.badge-dot-xl {
    width: 18px;
    height: 18px;
    position: relative;
}

.date-header {
    display: block;
    background: #f3f3f3;
    padding: 6px 10px;
    border-radius: 8px;
    padding-left: 41px;
}
.table-bordered {
    box-shadow: none;
    margin-bottom: 0;
}
</style>
<<<<<<< HEAD

<table class="table">
    <tr>
    <?php foreach ($timeline as $day => $item) {?>

    <td>
        <div style="background:#fff; padding:15px;border-radius:10px">
=======
<div style="background:#fff; padding:15px;border-radius:10px">
    <table class="table table-bordered">
        <tr>
        <?php foreach ($timeline as $day => $item) {?>
        
        <td>
>>>>>>> aed7c99a482603b7ad46eb1acebcf29ff1d9d159
            <b class="date-header"><?= date('d.m.Y', strtotime($day))  ?></b>
            <?php foreach ($item as $activity) {?>
            <div class="vertical-timeline">
                <div class="vertical-timeline-element">
                    <div>
                        <span class="vertical-timeline-element-icon bounce-in">
                            <i class="badge badge-dot badge-dot-xl badge-warning"> </i>
                        </span>
                        <div class="vertical-timeline-element-content">
                            <h4 class="timeline-title"><?= Activity::getDropdownList()[$activity->activity_id] ?? ($activity->activity_id == Activity::START_DAY ? 'Начало рабочего дня' : ($activity->activity_id == Activity::STOP_DAY ? 'Конец рабочего дня' : ''))  ?></h4>
                        <p><?= $activity->comment ?>

                        </p>
                        <span class="vertical-timeline-element-date"><?= date('H:i:s', strtotime($activity->datetime_at)) ?></span>
                        </div>
                    </div>
                </div>
            </div>
<<<<<<< HEAD
        
            <?php } ?>
        </div>
    </td>
    <?php } ?>
    </tr>
</table>
=======
            <?php } ?>
        </td>
        <?php } ?>
        </tr>
    </table>
</div>
>>>>>>> aed7c99a482603b7ad46eb1acebcf29ff1d9d159
