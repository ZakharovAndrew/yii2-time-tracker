<?php

use ZakharovAndrew\TimeTracker\Module;
use ZakharovAndrew\TimeTracker\models\Activity;
use ZakharovAndrew\TimeTracker\models\TimeTracking;
use ZakharovAndrew\user\models\User;
use yii\helpers\Html;
use yii\helpers\Url;
use ZakharovAndrew\TimeTracker\assets\TimeTrackerAssets;

TimeTrackerAssets::register($this);

$approved = $approved ?? false;
$module = Yii::$app->getModule('timetracker');
$blockEditing = $module->blockEditingForApproved;
$showEditButtons = $is_editor && (!$approved || !$blockEditing);
?>

<b class="timeline-header"><?= date('d.m.Y', strtotime($day))  ?>
    <?php
    $workTime = 0;
    $breakTime = 0;
    $activityCount = count($activities);
    foreach ($activities as $i => $activity) {
        $nextActivityTime = ($i === $activityCount - 1) ?  strtotime('now') : strtotime($activities[$i + 1]->datetime_at);

        $activityTime = $nextActivityTime - strtotime($activity->datetime_at);

        // sum up working hours
        if (!$activity->isWorkStop() && !$activity->isWorkBreak()) {
            $workTime += $activityTime;
        }

        // sum up breaking hours
        if ($activity->isWorkBreak()) {
            $breakTime += $activityTime;
        }

    }?>
    <span class="work_time" title="<?= Module::t('Working hours')?>"><?= Activity::timeFormat($workTime) ?></span>
    <?php
    echo '<span class="break_time">'.Activity::timeFormat($breakTime).'</span>';
    ?>
    <?php if ($showEditButtons) {?>
    <button type="button" class="btn btn-success btn-add-activity" data-toggle="modal" data-bs-toggle="modal" data-target="#form-add-activity" data-bs-target="#form-add-activity" data-day="<?= date('Y-m-d', strtotime($day))?>" title="<?= Module::t('Add Activity')?>">+</button>    
    <?php }?>
</b>

<div class="vertical-timeline">
    <?php foreach ($activities as $activity) {  ?>
        <div class="timeline-element">
            <div>
                <span class="timeline-icon">
                    <i class="badge badge-dot activity-<?= $activity->activity_id ?>"> </i>
                </span>
                <div class="timeline-content">
                    <h4 class="timeline-title"><?= Activity::getList()[$activity->activity_id] ?? ''  ?></h4>
                    <?php if (!empty($activity->datetime_update) && $activity->datetime_at <> $activity->datetime_update) { ?>
                        <div class="timeline-date-update">
                            <?= Module::t('Changed') ?>
                            <?php
                            if (date('Y-m-d', strtotime($activity->datetime_update)) != date('Y-m-d', strtotime($activity->datetime_at))) {
                                echo date('d.m.Y', strtotime($activity->datetime_update));
                            } ?>
                            <?= date('H:i:s', strtotime($activity->datetime_update)) ?>
                            <?php if (!empty($activity->who_changed) && $activity->who_changed != $activity->user_id) {
                                $who_changed = User::find()->where(['id' => $activity->who_changed])->one();
                                echo $who_changed->name;
                            } ?>
                        </div>
                    <?php } ?>
                    <?php
                    if ($showEditButtons) {
                        echo Html::a(Module::t('Edit'), ['update', 'id' => $activity->id], ['class' => 'btn btn-success btn-edit-activity']);
                        echo Html::a(Module::t('Delete'), Url::to(['delete', 'id' => $activity->id]), [
                            'class' => 'btn btn-danger btn-delete-activity',
                            'data' => [
                                'confirm' => Module::t('Are you sure you want to delete this item?'),
                            ],
                        ]);
                    }
                    ?>
                    <p><?= $activity->comment ?></p>
                    <span class="timeline-date"><?= date('H:i:s', strtotime($activity->datetime_at)) ?></span>
                </div>
            </div>
        </div>
    <?php } ?>
</div>