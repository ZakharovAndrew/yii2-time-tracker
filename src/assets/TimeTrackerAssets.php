<?php
/**
 * @link https://github.com/ZakharovAndrew/yii2-time-tracker
 * @copyright Copyright (c) 2024 Zakharov Andrey
 */

namespace ZakharovAndrew\TimeTracker\assets;

use yii\web\AssetBundle;

class TimeTrackerAssets extends AssetBundle
{
    public $sourcePath = '@vendor/zakharov-andrew/yii2-time-tracker/src/assets';

    public $css = [
        'css/style_v0.1.css',
    ];

    public $js = [
        'js/timetracker_v1.51.js',
    ];

    public $depends = [
        'yii\web\YiiAsset',
        //'yii\bootstrap5\BootstrapAsset',
    ];
}