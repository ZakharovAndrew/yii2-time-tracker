<?php

use yii\helpers\Html;
use ZakharovAndrew\TimeTracker\Module;
use ZakharovAndrew\TimeTracker\models\Activity;

/** @var yii\web\View $this */
?>
<table class="table">
    <thead>
        <tr>
            <th><?= Module::t('Activity') ?></th>
            <th class="time-tracking__time"><?= Module::t('Time') ?></th>
        </tr>
    </thead>

    <?php
    $labels = [];
    $colors = [];
    foreach ($aggActivity as $key => $activity) { 
        $labels[] = Activity::getList()[$key];
        $colors[] = Activity::getActivityColors()[$key] ?? '#4441bc';
        echo '<tr><td>'.Activity::getList()[$key].'</td><td>'. Activity::timeFormat($activity) .'</td></tr>';
    }
    ?>
</table>