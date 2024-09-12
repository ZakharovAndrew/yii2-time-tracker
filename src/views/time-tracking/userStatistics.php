<?php

use ZakharovAndrew\TimeTracker\Module;
use ZakharovAndrew\TimeTracker\models\Activity;
use ZakharovAndrew\TimeTracker\models\TimeTracking;
use ZakharovAndrew\user\models\User;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var ZakharovAndrew\TimeTracker\models\TimeTrackingSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = Module::t('Statistics') . ': '. $user->name;
$this->params['breadcrumbs'][] = ['label' => Module::t('Time Tracking'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$script = <<< JS
    let activity_day = '';
    $(".btn-add-activity").on('click', function() {
        $("#form-add-activity form").first().trigger('reset');
        activity_day = $(this).data('day');
    });
    $("#timetracking-activity_time").on('change keyup', function() {
        $("#timetracking-datetime_at").val(activity_day + ' '+$("#timetracking-activity_time").val());
    });
        
JS;

$this->registerJs($script, yii\web\View::POS_READY);
//echo Html::a('+', ['add', 'id' => $user->id, 'day' => date('Y-m-d', strtotime($day))], ['class' => 'btn btn-sm btn-primary btn-add-activity', 'title'=>Module::t('Add Activity')]);
?>
<?= $this->render('_timeline_style') ?>

<div style="background:#fff; padding:15px;border-radius:10px">
    <div class="table-responsive">
        <table class="table table-bordered">
            <tr>
            <?php foreach ($timeline as $day => $item) {?>

            <td>
                <b class="timeline-header"><?= date('d.m.Y', strtotime($day))  ?><?php if ($is_editor) {?>
                    <button type="button" class="btn btn-success btn-add-activity" data-toggle="modal" data-target="#form-add-activity" data-day="<?= date('Y-m-d', strtotime($day))?>" title="<?= Module::t('Add Activity')?>">+</button>
                    
                <?php }?></b>
                <?php foreach ($item as $activity) {?>
                <div class="vertical-timeline">
                    <div class="timeline-element">
                        <div>
                            <span class="timeline-icon">
                                <i class="badge badge-dot badge-dot-xl badge-warning activity-<?= $activity->activity_id ?>"> </i>
                            </span>
                            <div class="timeline-content">
                                <h4 class="timeline-title"><?= Activity::getList()[$activity->activity_id] ?? ''  ?></h4>
                                <?php if (!empty($activity->datetime_update) && $activity->datetime_at <> $activity->datetime_update) { ?>
                                    <div class="timeline-date-update">
                                        Изменено 
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
                                if ($is_editor) {
                                    echo Html::a(Module::t('Edit'), ['update', 'id' => $activity->id], ['class' => 'btn btn-success btn-edit-activity']);
                                }
                                ?>
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

<?php
if ($is_editor) {
    $bootstrapVersion = Yii::$app->getModule('timetracker')->bootstrapVersion;
    $classModal = "\\yii\bootstrap".($bootstrapVersion==3 ? '' : $bootstrapVersion)."\\Modal";
    
    // FORM
    $classModal::begin([
        ($bootstrapVersion==3 ? 'header' : 'title') => '<h2>'.Module::t('Add Activity').'</h2>',
        'id' => 'form-add-activity'
    ]);

    echo $this->render('_form_add_for_editor', [
        'model' => new TimeTracking(['user_id' => $user->id]),
        'user_id' => $user->id
    ]);

    $classModal::end();
}
?>