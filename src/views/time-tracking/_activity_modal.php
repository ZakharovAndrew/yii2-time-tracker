<?php

use ZakharovAndrew\TimeTracker\Module;
use ZakharovAndrew\TimeTracker\models\Activity;
use ZakharovAndrew\user\models\User;
use yii\widgets\ActiveForm;
use yii\helpers\Html;


$bootstrapVersion = Yii::$app->getModule('timetracker')->bootstrapVersion;
$classModal = "\\yii\bootstrap".($bootstrapVersion==3 ? '' : $bootstrapVersion)."\\Modal";

$str = Module::t('Activities');

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

<?php
$classModal::begin([
    'id' => 'popover-modal',
    ($bootstrapVersion==3 ? 'header' : 'title') => '<h2 id="popover-modal-title">'.Module::t('Activities').'</h2>',
]);

$classModal::end();
?>