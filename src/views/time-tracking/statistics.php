<?php

use ZakharovAndrew\TimeTracker\Module;
use ZakharovAndrew\TimeTracker\models\Activity;
use ZakharovAndrew\user\models\User;
use yii\widgets\ActiveForm;
use yii\helpers\Html;
use ZakharovAndrew\TimeTracker\assets\TimeTrackerAssets;

TimeTrackerAssets::register($this);

$bootstrapVersion = Yii::$app->getModule('timetracker')->bootstrapVersion;
$classModal = "\\yii\bootstrap".($bootstrapVersion==3 ? '' : $bootstrapVersion)."\\Modal";

/** @var yii\web\View $this */
/** @var ZakharovAndrew\TimeTracker\models\TimeTrackingSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = Module::t('Statistics');
$this->params['breadcrumbs'][] = ['label' => Module::t('Time Tracking'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

// for json
$activity_list = [];
$i = 1;

$script = <<< JS
$("h1").append('<div id="filter" class="btn mg-top--10"><svg height="18" width="18" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 300.906 300.906" xml:space="preserve"><path d="M288.953 0h-277c-5.522 0-10 4.478-10 10v49.531c0 5.522 4.478 10 10 10h12.372l91.378 107.397v113.978a10 10 0 0 0 15.547 8.32l49.5-33a9.999 9.999 0 0 0 4.453-8.32v-80.978l91.378-107.397h12.372c5.522 0 10-4.478 10-10V10c0-5.522-4.477-10-10-10zM167.587 166.77a9.999 9.999 0 0 0-2.384 6.48v79.305l-29.5 19.666V173.25a9.997 9.997 0 0 0-2.384-6.48L50.585 69.531h199.736l-82.734 97.239zM278.953 49.531h-257V20h257v29.531z"/></svg></div>');
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
<?= $this->render('_activity_modal') ?>
<style>
    h1 {display:inline-block}
    .form-users-list .search-box {
        margin-bottom:5px;
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
                    <?php foreach ($timeline as $day => $item) { $class = (date('N', strtotime($day)) > 5) ? 'td-holiday ' : '';?>
                    <th class="<?= $class ?>"><?= date('d.m.Y', strtotime($day))  ?></th>
                    <?php } ?>
                    
                </tr>
            </thead>
            
            <?php foreach ($users as $user_id => $user_name) {?>
            <tr>
                <td>
                    <?= Html::a($user_name, ['user-statistics', 'user_id' => $user_id], ['class' => '']) ?>
                </td>
                <?php foreach ($timeline as $day => $item) {
                    $class = (date('N', strtotime($day)) > 5) ? 'td-holiday ' : '';
                    /*$item_name = date('Y-m-d', strtotime($item[$user_id]->datetime_at));*/?>
                    <?php if ($item[$user_id] ?? '') {
                        $start = '';
                        $stop = '';
                        
                        foreach ($item[$user_id] as $activity) {
                            if ($activity->activity_id == Activity::WORK_START) {
                                $start = date('H:i', strtotime($activity->datetime_at));
                            }
                                            
                            $activity_list[$i][] = [
                                'id' => $activity->activity_id,
                                'activity' => $activities[$activity->activity_id],
                                'time' => date('H:i:s', strtotime($activity->datetime_at)),
                                'date' => date('d.m.Y', strtotime($activity->datetime_at)),
                                'comment' => $activity->comment ?? ''
                            ];
                        }
                        
                        // last status
                        $end_activity = end($item[$user_id]);
                        
                        // find stop day
                        if ($end_activity->activity_id == Activity::WORK_STOP) {
                            $stop = date('H:i', strtotime($activity->datetime_at));
                        }
                        
                        // not current day
                        if (date('d.m.Y', strtotime($day)) != date('d.m.Y') || !empty($stop)) {
                            $time_diff = round((strtotime($stop ?? $start) - strtotime($start))/3600);
                            if ($time_diff < 6) {
                                $class .= ' td-warning';
                            }
                            if (empty($stop)) {
                                $class .= ' td-danger';
                                $alert = '⚠ ';
                            }  
                        }
                        
                        
                        
                        
                        $hint = '';
                        if ($end_activity->activity_id != Activity::WORK_STOP && $end_activity->activity_id != Activity::WORK_START) {
                            $hint = '<div class="last_activity">'.$activities[$activity->activity_id].'</div>';
                        }
                        
                        echo '<td class="'.$class.'" data-id="'.$i.'" data-toggle="popover" data-user="'.$user_name.'" data-day="' . date('d.m.Y', strtotime($day)) .'"><span>'.$start.($stop ? ' - '.$stop : '').'</span>'.$hint.'</td>';
                        $i++;
                    } else {
                    ?>
                    <td class="<?= $class ?>"></td>
                    <?php } ?>
                <?php } ?>
            </tr>
            <?php } ?>
        </table>
    </div>
    
    <?php } ?>

</div>

<div class="settings-modal" data-modal-name="settings">
    <div class="settings-modal-title">
        <div><?= Module::t('Filter') ?></div>
        <div class="btn btn-modal-close">
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
    
    <label><?= Module::t('Users') ?></label>
    <div class="form-users-list">
        <div class="search-box"><?= Module::t('Filter') ?> <input type="text" id="users-list-filter" data-filter-item=".users-list-item" class="filter-control"></div>
        <?php
        $users = User::find()->where(['<>', 'status', User::STATUS_DELETED])->orderBy('name')->all();
        foreach ($users as $user) {
            $value = in_array($user->id, $selected_user_ids ?? []);
            echo '<div class="users-list-item">'.Html::checkbox('users[]', $value, ['value' => $user->id, 'label' => $user->name]).'</div>';
        } ?>
    </div>

    <div class="form-group bottom-panel">
        <?= Html::submitButton(Module::t('Apply'), ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>


<script>
    let data = <?= json_encode($activity_list, JSON_UNESCAPED_UNICODE) ?>;
</script>


