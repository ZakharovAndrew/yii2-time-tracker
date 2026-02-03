<?php

namespace ZakharovAndrew\TimeTracker\widgets;

use Yii;
use yii\base\Widget;
use yii\web\JsExpression;
use ZakharovAndrew\TimeTracker\models\TimeTracking;

/**
 * Widget for displaying a real-time timer counting seconds since last user activity
 * Only available for authenticated users. Updates every second client-side and syncs with server periodically via AJAX
 */
class TimeSinceLastActivityWidget extends Widget
{
    /**
     * @var int Client-side update interval in milliseconds (default: 1000ms = 1 second)
     */
    public $updateClientInterval = 1000;
    
    /**
     * @var int Server sync interval in milliseconds (default: 10000ms = 10 seconds)
     */
    public $updateServerInterval = 10000;
    
    /**
     * @var string CSS class for the widget container
     */
    public $cssClass = 'time-since-activity';
    
    /**
     * @var bool Auto-start timer on widget initialization
     */
    public $autoStart = true;
    
    /**
     * @var bool Show refresh button for manual sync
     */
    public $showRefreshButton = true;
    
    /**
     * @var array Time format presets: 'digital', 'seconds', 'minutes', 'compact'
     */
    public $format = 'digital'; // digital, seconds, minutes, compact
    
    /**
     * @var bool Show leading zeros in digital format
     */
    public $showLeadingZeros = true;
    
    /**
     * @var bool Show activity name below the timer
     */
    public $showActivityName = true;
    
    /**
     * @var int Max length for activity name (truncate if longer)
     */
    public $activityNameMaxLength = 30;
    
    /**
     * @var string Text when no activity name is available
     */
    public $noActivityNameText = 'No activity';
    
    /**
     * @var string Container HTML tag
     */
    public $containerTag = 'div';
    
    /**
     * @var array HTML options for container
     */
    public $containerOptions = [];
    
    /**
     * @var int|null Current user ID (automatically determined)
     */
    private $_userId;
    
    /**
     * @var string|null Current activity name
     */
    private $_activityName;

    private $_lastActivityId;

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();
        
        // Only proceed if user is authenticated
        if (Yii::$app->user->isGuest) {
            return;
        }
        
        $this->_userId = Yii::$app->user->id;
        
        // Get activity data
        $lastActivity = $this->getLastActivityData();
        if ($lastActivity && isset($lastActivity['last_activity_name'])) {
            $this->_activityName = $lastActivity['last_activity_name'];
        }
        
        // Generate unique ID for the widget
        if (!isset($this->containerOptions['id'])) {
            $this->containerOptions['id'] = 'time-since-activity-' . $this->_userId . '-' . uniqid();
        }
        
        // Add base CSS class
        if (isset($this->containerOptions['class'])) {
            $this->containerOptions['class'] .= ' ' . $this->cssClass;
        } else {
            $this->containerOptions['class'] = $this->cssClass;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        // Return empty string for guest users
        if (Yii::$app->user->isGuest) {
            return '';
        }
        
        // Get last activity data
        $lastActivity = $this->getLastActivityData();
        
        if (!$lastActivity || empty($lastActivity['last_activity_time'])) {
            return $this->renderNoActivity();
        }
        
        $this->_lastActivityId = $lastActivity['last_activity_id'] ?? 0;
        
        $initialTimestamp = strtotime($lastActivity['last_activity_time']);
        $initialSeconds = time() - $initialTimestamp;
        
        // Register client-side scripts
        $this->registerClientScript($initialTimestamp, $initialSeconds);
        
        // Render timer container
        $container = Yii::$app->view->renderFile('@vendor/zakharov-andrew/yii2-time-tracker/src/widgets/views/time-since-last-activity.php', [
            'widget' => $this,
            'initialSeconds' => $initialSeconds,
            'lastActivityTime' => $lastActivity['last_activity_time'],
            'lastActivityId' => $this->_lastActivityId,
            'activityName' => $this->_activityName ?? $this->noActivityNameText,
            'hasActivityName' => !empty($this->_activityName),
            'userId' => $this->_userId,
        ]);
        
        return $container;
    }
    
    /**
     * Get current user ID
     * @return int|null
     */
    public function getUserId()
    {
        return $this->_userId;
    }
    
    /**
     * Get last activity data from cache
     * @return array|null
     */
    protected function getLastActivityData()
    {
        if (!$this->_userId) {
            return null;
        }
        
        $usersData = TimeTracking::getUsersWithLastActivity();
        return $usersData[$this->_userId] ?? null;
    }
    
    /**
     * Truncate activity name if it's too long
     * @param string $name Activity name
     * @return string Truncated name
     */
    protected function truncateActivityName($name)
    {
        if (strlen($name) <= $this->activityNameMaxLength) {
            return $name;
        }
        
        return mb_substr($name, 0, $this->activityNameMaxLength - 3) . '...';
    }
    
    /**
     * Render "no activity" state
     * @return string
     */
    protected function renderNoActivity()
    {
        return "<{$this->containerTag} class='{$this->cssClass} state-no-activity'>
                    <span class='timer-display'>--:--:--</span>
                </{$this->containerTag}>";
    }
    
    /**
     * Register JavaScript for timer functionality
     * @param int $initialTimestamp Unix timestamp of last activity
     * @param int $initialSeconds Initial seconds since last activity
     */
    protected function registerClientScript($initialTimestamp, $initialSeconds)
    {
        if (!$this->_userId) {
            return;
        }
        
        $view = $this->getView();
        $containerId = $this->containerOptions['id'];
        
        // Create AJAX sync URL
        $syncUrl = Yii::$app->urlManager->createUrl([
            '/timetracker/time-tracking/get-last-activity'
        ]);
        
        // Create TimeTracker URL
        $timetrackerUrl = Yii::$app->urlManager->createUrl([
            '/timetracker/time-tracking/index'
        ]);
        
        // Prepare configuration for JavaScript
        $jsConfig = [
            'containerId' => $containerId,
            'updateClientInterval' => $this->updateClientInterval,
            'updateServerInterval' => $this->updateServerInterval,
            'syncUrl' => $syncUrl,
            'timetrackerUrl' => $timetrackerUrl,
            'initialTimestamp' => $initialTimestamp,
            'initialSeconds' => $initialSeconds,
            'format' => $this->format,
            'showLeadingZeros' => $this->showLeadingZeros,
            'showActivityName' => $this->showActivityName,
            'showRefreshButton' => $this->showRefreshButton,
            'autoStart' => $this->autoStart,
            'lastActivityId' => $this->_lastActivityId
        ];
        
        $js = new JsExpression("
            (function() {
                'use strict';
                
                /**
                 * Timer widget for displaying elapsed time since last activity
                 * @class TimeSinceActivityTimer
                 */
                function TimeSinceActivityTimer(config) {
                    this.config = config;
                    this.container = document.getElementById(config.containerId);
                    
                    if (!this.container) {
                        console.warn('Timer container not found:', config.containerId);
                        return;
                    }
                    
                    this.timerElement = this.container.querySelector('.timer-display');
                    this.activityNameElement = this.container.querySelector('.activity-name');
                    this.timerWrapperElement = this.container.querySelector('.timer-wrapper');
                    this.currentSeconds = config.initialSeconds;
                    this.lastSyncTime = Date.now();
                    this.timerInterval = null;
                    this.syncInterval = null;
                    this.isRunning = false;
                    this.lastActivityId = this.config.lastActivityId;
                    
                    // Bind refresh button click event
                    var refreshBtn = this.container.querySelector('.btn-refresh');
                    if (refreshBtn) {
                        var self = this;
                        refreshBtn.addEventListener('click', function() {
                            self.syncWithServer();
                        });
                    }
                    
                    if (config.autoStart) {
                        this.init();
                    }
                    
                    this.timerWrapperElement.addEventListener('click', function() {
                        document.location = config.timetrackerUrl;
                    });
                }
                
                TimeSinceActivityTimer.prototype = {
                    /**
                     * Initialize timer
                     */
                    init: function() {
                        if (this.isRunning || !this.container) return;
                        
                        this.startTimer();
                        this.startServerSync();
                        this.updateDisplay();
                        
                        this.isRunning = true;
                    },
                    
                    /**
                     * Start client-side timer interval
                     */
                    startTimer: function() {
                        var self = this;
                        this.timerInterval = setInterval(function() {
                            self.currentSeconds++;
                            self.updateDisplay();
                        }, this.config.updateClientInterval);
                    },
                    
                    /**
                     * Start periodic server synchronization
                     */
                    startServerSync: function() {
                        var self = this;
                        this.syncInterval = setInterval(function() {
                            self.syncWithServer();
                        }, this.config.updateServerInterval);
                    },
                    
                    /**
                     * Synchronize with server to get accurate last activity time
                     */
                    syncWithServer: function() {
                        var self = this;
                        
                        fetch(this.config.syncUrl + '?_=' + Date.now(), {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            credentials: 'same-origin'
                        })
                        .then(function(response) {
                            if (!response.ok) throw new Error('Network response was not ok');
                            return response.json();
                        })
                        .then(function(data) {
                            if (data.success && data.lastActivityTime) {
                                var serverTimestamp = Math.floor(new Date(data.lastActivityTime).getTime() / 1000);
                                var currentTimestamp = Math.floor(Date.now() / 1000);
                                var expectedSeconds = currentTimestamp - serverTimestamp;
                                
                                self.lastActivityId = data.lastActivityId;
                                
                                // Sync if difference is more than 2 seconds
                                if (Math.abs(self.currentSeconds - expectedSeconds) > 2) {
                                    self.currentSeconds = expectedSeconds;
                                    self.lastSyncTime = Date.now();
                                    self.updateDisplay();
                                }
                                
                                // Update activity name if available
                                if (self.config.showActivityName && data.lastActivityName && self.activityNameElement) {
                                    self.activityNameElement.textContent = data.lastActivityName;
                                }
                                
                                // stop
                                if (data.lastActivityId == 1000001) {
                                    self.container.style.display = 'none';
                                } else {
                                    self.container.style.display = 'block';
                                }
                            }
                        })
                        .catch(function(error) {
                            console.warn('Activity timer sync failed:', error.message);
                        });
                    },
                    
                    /**
                     * Format seconds into display string based on selected format
                     * @param {number} seconds - Total seconds
                     * @return {string} Formatted time string
                     */
                    formatTime: function(seconds) {
                        var hours = Math.floor(seconds / 3600);
                        var minutes = Math.floor((seconds % 3600) / 60);
                        var secs = seconds % 60;
                        
                        switch (this.config.format) {
                            case 'seconds':
                                return seconds.toString();
                                
                            case 'minutes':
                                var totalMinutes = Math.floor(seconds / 60);
                                var remainingSeconds = seconds % 60;
                                return totalMinutes + ':' + this.padZero(remainingSeconds, 2);
                                
                            case 'compact':
                                if (hours > 0) {
                                    return hours + 'h ' + minutes + 'm';
                                } else if (minutes > 0) {
                                    return minutes + 'm ' + secs + 's';
                                } else {
                                    return secs + 's';
                                }
                                
                            case 'digital':
                            default:
                                if (this.config.showLeadingZeros) {
                                    return this.padZero(hours, 2) + ':' + 
                                           this.padZero(minutes, 2) + ':' + 
                                           this.padZero(secs, 2);
                                } else {
                                    return hours + ':' + 
                                           this.padZero(minutes, 2) + ':' + 
                                           this.padZero(secs, 2);
                                }
                        }
                    },
                    
                    /**
                     * Pad number with leading zeros
                     * @param {number} num - Number to pad
                     * @param {number} size - Total size after padding
                     * @return {string} Padded string
                     */
                    padZero: function(num, size) {
                        var s = num.toString();
                        while (s.length < size) s = '0' + s;
                        return s;
                    },
                    
                    /**
                     * Update timer display
                     */
                    updateDisplay: function() {
                        if (!this.timerElement || this.lastActivityId == 1000001) return;
                        this.timerElement.textContent = this.formatTime(this.currentSeconds);
                    },
                    
                    /**
                     * Manual refresh button handler
                     */
                    refresh: function() {
                        this.syncWithServer();
                    },
                    
                    /**
                     * Get current elapsed seconds
                     * @return {number} Seconds since last activity
                     */
                    getSeconds: function() {
                        return this.currentSeconds;
                    },
                    
                    /**
                     * Stop timer
                     */
                    stop: function() {
                        if (this.timerInterval) {
                            clearInterval(this.timerInterval);
                            this.timerInterval = null;
                        }
                        
                        if (this.syncInterval) {
                            clearInterval(this.syncInterval);
                            this.syncInterval = null;
                        }
                        
                        this.isRunning = false;
                    },
                    
                    /**
                     * Destroy timer and clean up
                     */
                    destroy: function() {
                        this.stop();
                        this.container = null;
                        this.timerElement = null;
                    }
                };
                
                // Initialize timer
                window.timeSinceActivityTimer_{$this->_userId} = new TimeSinceActivityTimer(" . json_encode($jsConfig) . ");
                
            })();
        ");
        
        $view->registerJs($js);
    }
}