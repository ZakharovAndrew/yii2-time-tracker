<?php

/**
 * Yii2 Time tracker
 * *************
 * Yii 2 module for time tracking. The time tracking module for the Yii2 framework 
 * is a comprehensive solution for monitoring time spent on various tasks within
 * projects. It allows users to easily start, stop, and record time intervals 
 * for specific tasks, as well as provides detailed reports on time spent.
 *  
 * @link https://github.com/ZakharovAndrew/yii2-time-tracking/
 * @copyright Copyright (c) 2024 Zakharov Andrew
 */
 
namespace ZakharovAndrew\TimeTracker;

use Yii;

/**
 * Time tracker module
 */
class Module extends \yii\base\Module
{    
    /**
     * @var string Module version
     */
    protected $version = "0.1.0";

    /**
     * @var string Alias for module
     */
    public $alias = "@timetracker";
    
    /**
     * @var string version Bootstrap
     */
    public $bootstrapVersion = '';
 
    public $useTranslite = false;
    
    /**
     * @var array user role and available roles for viewing statistics
     */
    public $availableRolesForViewingStatistics = [
        'admin' => '*'
    ];
    
    /**
     * @var array list of user properties in statistics
     */
    public $statisticUserProperties = [
        
    ];
    
    /**
     * @var string show H1
     */
    public $showTitle = true;
    
    /**
     *
     * @var string source language for translation 
     */
    public $sourceLanguage = 'en-US';
    
    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'ZakharovAndrew\TimeTracker\controllers';
    

    /**
     * {@inheritdoc}
     * @throws \yii\base\InvalidConfigException
     */
    public function init()
    {
        parent::init();
        $this->registerTranslations();
    }
    
    /**
     * Registers the translation files
     */
    protected function registerTranslations()
    {
        Yii::$app->i18n->translations['extension/yii2-time-tracker/*'] = [
            'class' => 'yii\i18n\PhpMessageSource',
            'sourceLanguage' => $this->sourceLanguage,
            'basePath' => '@vendor/zakharov-andrew/yii2-time-tracker/src/messages',
            'on missingTranslation' => ['app\components\TranslationEventHandler', 'handleMissingTranslation'],
            'fileMap' => [
                'extension/yii2-time-tracker/time-tracker' => 'time_tracker.php',
            ],
        ];
    }

    /**
     * Translates a message. This is just a wrapper of Yii::t
     *
     * @see Yii::t
     *
     * @param $category
     * @param $message
     * @param array $params
     * @param null $language
     * @return string
     */
    public static function t($message, $params = [], $language = null)
    {
        $category = 'time-tracker';
        return Yii::t('extension/yii2-time-tracker/' . $category, $message, $params, $language);
    }
    
}
