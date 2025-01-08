<?php

use ZakharovAndrew\TimeTracker\Module;
use ZakharovAndrew\TimeTracker\models\Activity;
use ZakharovAndrew\TimeTracker\models\TimeTracking;
use ZakharovAndrew\user\models\User;
use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var ZakharovAndrew\TimeTracker\models\TimeTrackingSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = Module::t('Statistics') . ': '. $user->name;
$this->params['breadcrumbs'][] = ['label' => Module::t('Time Tracking'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$script = <<< JS
    $("h1").append('<div id="filter" class="btn mg-top--10"><svg height="18" width="18" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 300.906 300.906" xml:space="preserve"><path d="M288.953 0h-277c-5.522 0-10 4.478-10 10v49.531c0 5.522 4.478 10 10 10h12.372l91.378 107.397v113.978a10 10 0 0 0 15.547 8.32l49.5-33a9.999 9.999 0 0 0 4.453-8.32v-80.978l91.378-107.397h12.372c5.522 0 10-4.478 10-10V10c0-5.522-4.477-10-10-10zM167.587 166.77a9.999 9.999 0 0 0-2.384 6.48v79.305l-29.5 19.666V173.25a9.997 9.997 0 0 0-2.384-6.48L50.585 69.531h199.736l-82.734 97.239zM278.953 49.531h-257V20h257v29.531z"/></svg></div>');
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
<style>
.table-user-statistics {
    width: auto;
    height: calc(100vh - 236px);
    border-radius: 15px;
    padding: 15px;
    background: #fff;
    display: flex;
</style>

<?php if (Yii::$app->getModule('timetracker')->showTitle) {?><h1><?= Html::encode($this->title) ?></h1><?php } ?>

<div style="background:#fff; padding:15px;border-radius:10px">
    <div class="table-responsive table-user-statistics">
        <table class="table table-bordered">
            <tr>
            <?php foreach ($timeline as $day => $item) {?>

            <td>
                <b class="timeline-header"><?= date('d.m.Y', strtotime($day))  ?><?php if ($is_editor) {?>
                    <button type="button" class="btn btn-success btn-add-activity" data-toggle="modal" data-bs-toggle="modal" data-target="#form-add-activity" data-bs-target="#form-add-activity" data-day="<?= date('Y-m-d', strtotime($day))?>" title="<?= Module::t('Add Activity')?>">+</button>
                    
                <?php }?></b>
                <div class="vertical-timeline">
                <?php foreach ($item as $activity) {?>
                    <div class="timeline-element">
                        <div>
                            <span class="timeline-icon">
                                <i class="badge badge-dot activity-<?= $activity->activity_id ?>"> </i>
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
                                    echo Html::a('Удалить', Url::to(['delete', 'id' => $activity->id]), [
                                        'class' => 'btn btn-danger btn-delete-activity',
                                        'data' => [
                                            'confirm' => 'Вы уверены, что хотите удалить этот элемент?',
                                            //'method' => 'get',
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