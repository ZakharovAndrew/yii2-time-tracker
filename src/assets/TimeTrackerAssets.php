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
        'css/style.css',
    ];

    public $js = [
    //    'js/script.js',
    ];

    public $depends = [
        'yii\web\YiiAsset',
        //'yii\bootstrap5\BootstrapAsset',
    ];
}