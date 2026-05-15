<?php

use ZakharovAndrew\TimeTracker\Module;
use ZakharovAndrew\TimeTracker\models\Activity;
use ZakharovAndrew\TimeTracker\models\ActivityProperty;
use ZakharovAndrew\TimeTracker\models\TimeTracking;
use ZakharovAndrew\user\models\User;
use yii\helpers\Html;
use yii\helpers\Url;
use ZakharovAndrew\TimeTracker\assets\TimeTrackerAssets;

TimeTrackerAssets::register($this);

$showProperties = $show_properties ?? false;
$approved = $approved ?? false;
$module = Yii::$app->getModule('timetracker');
$blockEditing = $module->blockEditingForApproved;
$showEditButtons = $is_editor && (!$approved || !$blockEditing);
$properties = $properties ?? [];
$activityList = Activity::getList();

// Detect Bootstrap version for modal window compatibility (supports BS4 and BS5)
$bootstrapVersion = $module->bootstrapVersion;
$isBs5 = ($bootstrapVersion == 5);

// Calculate total working hours and break time
[$workTime, $breakTime] = TimeTracking::calculateWorkBreakTime($activities);
?>

<b class="timeline-header"><?= date('d.m.Y', strtotime($day))  ?>
    <span class="work_time" title="<?= Module::t('Working hours')?>"><?= Activity::timeFormat($workTime) ?></span>
    <span class="break_time"><?= Activity::timeFormat($breakTime) ?></span>
    <?php if ($showEditButtons) {?>
    <button type="button" class="btn btn-success btn-add-activity" 
        <?= $isBs5 ? 'data-bs-toggle="modal" data-bs-target="#form-add-activity"' : 'data-toggle="modal" data-target="#form-add-activity"' ?>
        data-day="<?= date('Y-m-d', strtotime($day)) ?>" 
        title="<?= Module::t('Add Activity') ?>">+</button>
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
                    <h4 class="timeline-title"><?= $activityList[$activity->activity_id] ?? ''  ?></h4>
                    <?php if (!empty($activity->datetime_update) && $activity->datetime_at != $activity->datetime_update) { ?>
                        <div class="timeline-date-update">
                            <?= Module::t('Changed') ?>
                            <?php
                            if (date('Y-m-d', strtotime($activity->datetime_update)) != date('Y-m-d', strtotime($activity->datetime_at))) {
                                echo date('d.m.Y', strtotime($activity->datetime_update));
                            } ?>
                            <?= date('H:i:s', strtotime($activity->datetime_update)) ?>
                            <?php 
                            // Display who made the changes if it wasn't the original user
                            if (!empty($activity->who_changed) && $activity->who_changed != $activity->user_id) {
                                $who_changed = User::find()->select('name')->where(['id' => $activity->who_changed])->one();
                                echo $who_changed ? $who_changed->name : '';
                            } ?>
                        </div>
                    <?php } ?>
                    <?php if ($showProperties) {?>
                    <div class="timeline-activity-properties">
                        <?php foreach ($activity->getUserActivityProperties() as $userActivityProperty) {
                            $property = $properties[$userActivityProperty->property_id] ?? null;
                            $propertyType = $property->type ?? '';
                            if (!empty($userActivityProperty->values) || $propertyType == ActivityProperty::TYPE_CHECKBOX) { ?>
                        <div class="timeline-activity-property timeline-activity-property__<?= $userActivityProperty->property_id ?>">
                            <span class="timeline-activity-property__name"><?= $property->name ?? '' ?>:</span>
                            <span class="timeline-activity-property__value"><?php 
                            if ($propertyType  == ActivityProperty::TYPE_CHECKBOX) {
                                echo $userActivityProperty->values == true ? Module::t('Yes') : Module::t('No');
                            } else {
                                echo $userActivityProperty->values ?? '' ;
                            }
                            ?></span>
                        </div>
                        <?php 
                            }
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
                    <p><?= Html::encode($activity->comment) ?></p>
                    <span class="timeline-date"><?= date('H:i:s', strtotime($activity->datetime_at)) ?></span>
                </div>
            </div>
        </div>
    <?php } ?>
</div>