<?php
use yii\helpers\Html;

/* @var $widget \ZakharovAndrew\TimeTracker\widgets\TimeSinceLastActivityWidget */
/* @var $initialSeconds int */
/* @var $lastActivityTime string */
/* @var $userId int */
?>

<?= Html::beginTag($widget->containerTag, $widget->containerOptions) ?>
<style>
.navbar-timer {
    margin: 0 15px;
    padding: 4px 8px;
    background: rgba(255, 255, 255, 0.1);
    border: none;
    border-radius: 4px;
    <?php if ($lastActivityId == ZakharovAndrew\TimeTracker\models\Activity::WORK_STOP) {?>
    display:none;
    <?php } ?>
    align-content: center;
}
.navbar-timer .timer-wrapper {
    gap: 6px;
    cursor:pointer;
}

.navbar-timer .timer-icon {
    color: rgba(255, 255, 255, 0.7);
    font-size: 14px;
}

.navbar-timer .timer-display {
    color: #1c1e13;
    font-size: 14px;
    font-weight: 500;
}
.navbar-timer .timer-progress {
    display: none; /* Скрываем прогресс-бар в навбаре */
}
.navbar-timer .timer-wrapper {
    background: #a2ed2d;
    display: flex;
    padding: 4px 11px 2px;
    border-radius: 8px;
    font-family: Roboto, Tahoma;
    margin: auto;
    line-height: 20px;
    border-bottom: 2px solid #7eb529;
}
.navbar-timer .timer-wrapper .activity-name {
    max-width: 100px;
    text-overflow: ellipsis;
    overflow: hidden;
    white-space: nowrap;
    font-size: 11px;
    line-height: 20px;
}
</style>
    <div class="timer-wrapper">
        
        <div class="timer-display">
            <?php
            // Initial display before JavaScript takes over
            $hours = floor($initialSeconds / 3600);
            $minutes = floor(($initialSeconds % 3600) / 60);
            $seconds = $initialSeconds % 60;
            
            if ($widget->format === 'digital') {
                if ($widget->showLeadingZeros) {
                    echo sprintf("%02d:%02d:%02d", $hours, $minutes, $seconds);
                } else {
                    echo $hours . ':' . sprintf("%02d:%02d", $minutes, $seconds);
                }
            } elseif ($widget->format === 'seconds') {
                echo $initialSeconds;
            } elseif ($widget->format === 'minutes') {
                echo floor($initialSeconds / 60) . ':' . sprintf("%02d", $seconds);
            } elseif ($widget->format === 'compact') {
                if ($hours > 0) {
                    echo $hours . 'h ' . $minutes . 'm';
                } elseif ($minutes > 0) {
                    echo $minutes . 'm ' . $seconds . 's';
                } else {
                    echo $seconds . 's';
                }
            }
            ?>
        </div>
        
        <?php if ($widget->showRefreshButton): ?>
            <div class="timer-controls">
                <button type="button" class="btn-refresh" title="Sync with server">
                    <i class="fa fa-refresh"></i>
                </button>
            </div>
        <?php endif; ?>
        
        <?php if ($widget->showActivityName): ?>
        <div class="activity-name" title="<?= Html::encode($hasActivityName ? $activityName : '') ?>">
            <?= Html::encode($activityName) ?>
        </div>
        <?php endif; ?>
    </div>
    
    <div class="timer-progress">
        <div class="progress-bar"></div>
    </div>
<?= Html::endTag($widget->containerTag) ?>