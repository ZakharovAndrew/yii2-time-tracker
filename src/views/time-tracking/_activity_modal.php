<?php

use ZakharovAndrew\TimeTracker\Module;
use ZakharovAndrew\TimeTracker\models\Activity;
use ZakharovAndrew\user\models\User;
use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;

$urlUserTimeline = Url::to(['user-timeline']);

$bootstrapVersion = Yii::$app->getModule('timetracker')->bootstrapVersion;
$classModal = "\\yii\bootstrap".($bootstrapVersion==3 ? '' : $bootstrapVersion)."\\Modal";

$script = <<< JS
        
$('[data-toggle="popover"]').click(function() {
    let id = $(this).data('id');
    let user_id = $(this).parent().data('user_id');
    let user_name = $(this).parent().data('username');
    let day = $(this).data('day');
     
    // Отправляем AJAX-запрос
    $.ajax({
        url: '{$urlUserTimeline}',
        type: 'GET',
        data: {
            date_event: day,
            user_id: user_id
        },
        //dataType: 'json',
        success: function(response) {
            if (response) {
                 // prepare and show modal
                $("#popover-modal .modal-body").html(response);
                $("#popover-modal-title").html(user_name);
                $('#popover-modal').modal('show');
            }
        },
        error: function(xhr, status, error) {
            $("#popover-modal-title").html('<div class="alert alert-danger">Ошибка при загрузке данных</div>');
            console.error('Error AJAX:', error);
        }
    });
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
