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
    protected $version = "0.1.8";

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
     * @var array list of user properties in dashboards
     */
    public $dashboardUserProperties = [
        
    ];
 
    /**
     * @var array list of activity properties ID for display in statistics
     */
    public $additionalPropertiesInWorkStatuses = [];
 
    /**
     * @var string show H1
     */
    public $beforeSaveFunction = '';
    
    /**
     * @var boolean show H1
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
     * @var array additional tabs for display
     * Configuration example:
     * [
     *     [
     *         'label' => 'Reports', // tab label
     *         'view' => 'reports', // view file name
     *         'params' => ['data' => $someData], // parameters for the view (optional) - can be either array or callable function that returns array
     *         'active' => false // whether the tab is active by default (optional)
     *     ],
     * ]
     * 
     * Note: 'params' can be defined in two ways:
     * 1. As an array: ['data' => $someData, 'otherParam' => $value]
     * 2. As a callable function that returns an array: function() use ($someData) { return ['data' => $someData]; }
     */
    public $additionalTabs = [];
    
    
    /**
     * @var callable Function that will be called after approval is saved
     * Function accepts parameters:
     * - $user_id: int
     * - $approval_date: string
     * 
     * Example usage in config:
     * 'afterApprovalFunction' => function($user_id, $approval_date) {
     *     // Send notification, call API, etc.
     *     Yii::info("Approval for user {$user_id} on {$approval_date}", 'approval');
     * }
     */
    public $afterApprovalFunction;
    
    /**
     * @var callable Function that will be called after time tracking record is updated
     * Function accepts parameters:
     * - $model: TimeTracking object (the updated record)
     * - $changedAttributes: array of changed attributes
     * 
     * Example usage in config:
     * 'afterUpdateFunction' => function($model, $changedAttributes) {
     *     // Send notification, log changes, call API, etc.
     *     Yii::info("Record {$model->id} was updated. Changes: " . json_encode($changedAttributes), 'timetracker');
     * }
     */
    public $afterUpdateFunction;
    
    /**
     * @var callable Function that will be called after time tracking record is deleted
     * Function accepts parameters:
     * - $model: TimeTracking object (the deleted record)
     * 
     * Example usage in config:
     * 'afterDeleteFunction' => function($model) {
     *     // Send notification, log deletion, call API, etc.
     *     Yii::info("Record {$model->id} was deleted by user " . Yii::$app->user->id, 'timetracker');
     *     // Or restore from backup, update external system, etc.
     * }
     */
    public $afterDeleteFunction;
    
    /**
     * @var callable Function that will be called after time tracking record is inserted (created)
     * Function accepts parameters:
     * - $model: TimeTracking object (the inserted record)
     * 
     * Example usage in config:
     * 'afterCreateFunction' => function($model) {
     *     // Send notification, call API, update external system, etc.
     *     Yii::info("New record {$model->id} was created by user " . Yii::$app->user->id, 'timetracker');
     *     // Or send email, push to CRM, etc.
     * }
     */
    public $afterCreateFunction;
    
    /**
     * @var boolean Block editing of approved time tracking records
     * If true, editing/deleting will be disabled for approved records
     * If false, editing/deleting will be allowed regardless of approval status
     */
    public $blockEditingForApproved = true;
    
    /**
     * {@inheritdoc}
     * @throws \yii\base\InvalidConfigException
     */
    public function init()
    {
        parent::init();
        static::registerTranslations();
    }
    
    /**
     * Registers the translation files
     */
    protected static function registerTranslations()
    {
        if (isset(Yii::$app->i18n->translations['extension/yii2-time-tracker/*'])) {
            return;
        }

        Yii::$app->i18n->translations['extension/yii2-time-tracker/*'] = [
            'class' => 'yii\i18n\PhpMessageSource',
            'sourceLanguage' => 'en-US',
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
        static::registerTranslations();

        $category = 'time-tracker';
        return Yii::t('extension/yii2-time-tracker/' . $category, $message, $params, $language);
    }
    
}
