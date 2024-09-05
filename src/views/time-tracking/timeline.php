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
<?= $this->render('_timeline_style') ?>

<div style="background:#fff; padding:15px;border-radius:10px">
    <div class="table-responsive">
        <table class="table table-bordered">
            <tr>
            <?php foreach ($timeline as $day => $item) {?>

            <td>
                <b class="timeline-header"><?= date('d.m.Y', strtotime($day))  ?></b>
                <?php foreach ($item as $activity) {?>
                <div class="vertical-timeline">
                    <div class="timeline-element">
                        <div>
                            <span class="timeline-icon">
                                <i class="badge badge-dot badge-dot-xl badge-warning activity-<?= $activity->activity_id ?>"> </i>
                            </span>
                            <div class="timeline-content">
                                <h4 class="timeline-title"><?= Activity::getDropdownList()[$activity->activity_id] ?? ($activity->activity_id == Activity::START_DAY ? 'Начало рабочего дня' : ($activity->activity_id == Activity::STOP_DAY ? 'Конец рабочего дня' : ''))  ?></h4>
                                <p><?= $activity->comment ?></p>
                                <span class="timeline-date"><?= date('H:i:s', strtotime($activity->datetime_at)) ?></span>
                            </div>
                        </div>
                    </div>
                </div>

                <?php } ?>
            </div>
        </td>
        <?php } ?>
        </tr>
        </table>
    </div>
</div>
