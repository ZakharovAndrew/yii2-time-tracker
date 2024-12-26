<?php

namespace ZakharovAndrew\TimeTracker\controllers;

use Yii;
use ZakharovAndrew\TimeTracker\Module;
use ZakharovAndrew\TimeTracker\models\TimeTracking;
use ZakharovAndrew\TimeTracker\models\Activity;
use ZakharovAndrew\TimeTracker\models\ActivityProperty;
use ZakharovAndrew\TimeTracker\models\UserActivityProperty;
use ZakharovAndrew\user\models\User;
use ZakharovAndrew\user\models\UserSettings;
use ZakharovAndrew\user\models\UserSettingsConfig;
use ZakharovAndrew\user\controllers\ParentController;
use yii\web\NotFoundHttpException;
use \yii\helpers\ArrayHelper;

/**
 * TimeTrackingController implements the CRUD actions for TimeTracking model.
 * @author Andrew Zakharov https://github.com/ZakharovAndrew
 */
class TimeTrackingController extends ParentController
{
    
    public $controller_id = 5001;

    /**
     * Lists all TimeTracking models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $user_activity = TimeTracking::getUserActivity(Yii::$app->user->id);
        
        $aggActivity = [];
        
        $activityCount = count($user_activity);
        $workTime = 0;
        $breakTime = 0;
        
        foreach ($user_activity as $i => $activity) {
            $nextActivityTime = ($i === $activityCount - 1) ?  strtotime('now') : strtotime($user_activity[$i + 1]->datetime_at);
            
            $activityTime = $nextActivityTime - strtotime($activity->datetime_at);

            if (!$activity->isWorkStop()) {
                $aggActivity[$activity->activity_id] = ($aggActivity[$activity->activity_id] ?? 0) + $activityTime;
            }
            
            // sum up working hours
            if (!$activity->isWorkStop() && !$activity->isWorkBreak()) {
                $workTime += $activityTime;
            }
            
            // sum up breaking hours
            if ($activity->isWorkBreak()) {
                $breakTime += $activityTime;
            }
            
        }
        
        // for chart js
        $labels = [];
        $colors = [];
        foreach ($aggActivity as $key => $activity) { 
            $labels[] = Activity::getList()[$key];
            $colors[] = Activity::getActivityColors()[$key] ?? '#4441bc';
        }
       
        return $this->render('index', [
            'user_activity' => $user_activity,
            'allow_statistics' => count(TimeTracking::userRolesForViewingStatistics()) >0,
            'aggActivity' => $aggActivity,
            'labels' => $labels,
            'colors' => $colors,
            'workTime' => $workTime,
            'breakTime' => $breakTime
        ]);
    }
    
    /**
     * Start the working day
     * 
     * @return mixed
     */
    public function actionStart()
    {
        // get last activity
        $user_activity = TimeTracking::getUserLastActivity(Yii::$app->user->id);
        
        if (!$user_activity || $user_activity->activity_id == Activity::WORK_STOP) {
            $model = new TimeTracking([
                'user_id' => Yii::$app->user->id,
                'activity_id' => Activity::WORK_START
            ]);
            
            if ($model->save()) {
                Yii::$app->session->setFlash('success', Module::t('The start of the working day is marked'));
                Yii::$app->session['add_activity'] = 1;
            } else {
                Yii::$app->session->setFlash('error', Module::t('Error in setting the start date of work.'));
            }
            
            return $this->redirect('index');
        }
        
        Yii::$app->session->setFlash('error', Module::t('The start of work has already been marked'));
        
        return $this->redirect('index');
    }
    
    public function canAddActivity()
    { 
        $user_activity = TimeTracking::getUserActivity(Yii::$app->user->id);
     
        // you cannot enter an activity if the working day has not started
        if (!$user_activity) {
            Yii::$app->session->setFlash('error', Module::t("You haven't started work yet!"));
            return $this->redirect('index');
        }
        
        $last_activity = end($user_activity);
        
        //You cannot enter an activity if the working day is over
        if ($last_activity->activity_id == Activity::WORK_STOP) {
            Yii::$app->session->setFlash('error', Module::t("You've already finished your work day!"));
            return $this->redirect('index');
        }
        
        return true;
    }
    
    public function actionBreak()
    {   
        if (($result = $this->canAddActivity()) !== true) {
            return $result;
        }
        
        $model = new TimeTracking([
            'user_id' => Yii::$app->user->id,
            'activity_id' => Activity::WORK_BREAK
        ]);
        
        if ($model->save()) {
            Yii::$app->session->setFlash('success', Module::t('The break of the working day is marked'));
        } else {
            Yii::$app->session->setFlash('error', Module::t('Error setting the break'));
        }
        
        return $this->redirect('index');
    }
    
    /**
     * Finish the working day
     * 
     * @return mixed
     */
    public function actionStop()
    {
        if (($result = $this->canAddActivity()) !== true) {
            return $result;
        }
        
        $model = new TimeTracking([
            'user_id' => Yii::$app->user->id,
            'activity_id' => Activity::WORK_STOP
        ]);
        
        if ($model->save()) {
            Yii::$app->session->setFlash('success', Module::t('The end of the working day is marked'));
        } else {
            Yii::$app->session->setFlash('error', Module::t('Error in setting the start date of work.'));
        }
        
        return $this->redirect('index');
    }
    
    public function actionStatistics($datetime_start = null, $datetime_stop = null, $username = null, $show_only_bad = null)
    {
        $selectedUserIds = Yii::$app->request->get('users');
        $selected_settings = Yii::$app->request->get('selected_settings');
        
        
        $roles = array_keys(Yii::$app->getModule('timetracker')->availableRolesForViewingStatistics);
        
        if (!Yii::$app->user->identity->hasRole($roles)) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
        
        // start of interval
        $start_day = !empty($datetime_start) ? $datetime_start : date('Y-m-d 00:00:00', strtotime('-7 days'));
        $stop_day = !empty($datetime_stop) ? $datetime_stop : date('Y-m-d 23:59:59');
        
        $roles = [];
        foreach (TimeTracking::userRolesForViewingStatistics() as $params) {
            if ($params == '*') {
                $roles = [];
                break;
            }
            
            $roles = array_merge(explode(',', $params), $roles);
        }
        
        
        // user filter
        $setting_i = 1;
        if (isset($selected_settings) && is_array($selected_settings)) {
            $query = User::find()->alias('u')->select('u.id');
            
            foreach ($selected_settings as $setting_name => $setting_value) {
                $query->leftJoin(UserSettingsConfig::tableName(). ' s'.$setting_i,
                        ['s'.$setting_i.".code" => $setting_name]
                    );
                $query->innerJoin(UserSettings::tableName(). ' us'.$setting_i,
                        "us{$setting_i}.setting_config_id = s{$setting_i}.id AND us{$setting_i}.user_id = u.id"  
                    );
                $query->andWhere(['us'.$setting_i.'.values' => $setting_value]);
                $setting_i++;
            }
            
            // if user IDs are specified
            if (!empty($selectedUserIds)) {
                $query->andWhere(['u.id' => $selectedUserIds]);
            }

            $users = ArrayHelper::getColumn($query->asArray()->all(), 'id');
            
            if (count($users) == 0) {
                $model = [
                    'days' => null,
                    'users_id' => null
                ];
            } else {
                $model = TimeTracking::getActivityByDay($start_day, $stop_day, $users, $roles);
            }
            
        } else {
            $model = TimeTracking::getActivityByDay($start_day, $stop_day, $selectedUserIds, $roles);
        }

        
        
        $settings = UserSettingsConfig::find()->where([
            'access_level' => [
                UserSettingsConfig::CHANGE_USER_AND_ADMIN,
                UserSettingsConfig::CHANGE_USER_ONLY,
                UserSettingsConfig::CHANGE_ADMIN_ONLY
            ]
        ])->all();
          
        return $this->render('statistics', [
            'timeline' => $model['days'],
            'datetime_start' => $datetime_start,
            'datetime_stop' => $datetime_stop,
            'show_only_bad' => $show_only_bad,
            'activities' => Activity::getList(),
            'selected_user_ids' => $selectedUserIds,
            'selected_settings' => $selected_settings,
            'users' => ArrayHelper::map(
                        User::find()
                            ->where(['id' => $model['users_id']])
                            ->orderBy('name')
                            ->all(),
                        'id', 'name'
                    ),
            'settings' => $settings,
            'user_properties_column' => UserSettingsConfig::find()->where([
                'code' => Yii::$app->getModule('timetracker')->statisticUserProperties
            ])->all()
        ]);
    }
    
    public function actionUserStatistics($user_id = null, $datetime_start = null, $datetime_stop = null)
    {
        if (!is_null($user_id) && !Yii::$app->user->identity->hasRole('admin') && !Yii::$app->user->identity->hasRole('time_tracking_editor')) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
        
        if (is_null($user_id)) {
            $user_id = Yii::$app->user->id;
        } else {
            $user_id = (Yii::$app->user->identity->hasRole('admin') || Yii::$app->user->identity->hasRole('time_tracking_editor')) ? $user_id : Yii::$app->user->id; 
        }
        
        $model = TimeTracking::find()
                ->where(['user_id' => $user_id])
                ->andWhere(['>', 'datetime_at', date('Y-m-d 00:00:00', strtotime($datetime_start ?? '-7 days'))])
                ->andWhere(['<', 'datetime_at', date('Y-m-d 23:59:59', strtotime($datetime_stop ?? 'now'))])
                ->orderBy('datetime_at')
                ->all();
        
        $timeline = [];
        foreach ($model as $item) {
            $item_name = date('Y-m-d', strtotime($item->datetime_at));
            $timeline[$item_name][] = $item;
        }
        
        return $this->render('userStatistics', [
            'timeline' => $timeline,
            'user' => User::findOne($user_id),
            'is_editor' => Yii::$app->user->identity->hasRole('time_tracking_editor')
        ]);
    }

    /**
     * Displays a single TimeTracking model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new TimeTracking model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        if (($result = $this->canAddActivity()) !== true) {
            return $result;
        }
        
        $model = new TimeTracking();
        
        $model->user_id = Yii::$app->user->id;

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                
                // save user activity property
                $properties = ActivityProperty::find()->all();
                foreach ($properties as $property) {
                    $value = Yii::$app->request->post($property->id) ?? null;
                    UserActivityProperty::saveValue(Yii::$app->user->id, $property->id, $model->id, $value);
                }
                
                return $this->redirect(['index']);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }
    
    /**
     * 
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException
     */
    public function actionAdd()
    {
        if (!Yii::$app->user->identity->hasRole('time_tracking_editor')) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
              
        $model = new TimeTracking();
        
        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                Yii::$app->session->setFlash('success', Module::t('Activity added'));
                
                // save user activity property
                $properties = ActivityProperty::find()->all();
                foreach ($properties as $property) {
                    $value = Yii::$app->request->post($property->id) ?? null;
                    UserActivityProperty::saveValue($model->user_id, $property->id, $model->id, $value);
                }
                
                return $this->redirect(['user-statistics', 'user_id' => $model->user_id]);
            }
        } 
        
        Yii::$app->session->setFlash('error', Module::t('Error when adding activity to a user.'));
        
        return $this->redirect(['index']);
    }
    
    public function actionEditComment()
    {
        $model = TimeTracking::find()
                ->where(['user_id' => Yii::$app->user->id])
                ->orderBy('id DESC')
                ->one();
        
        if (!$model) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
        
        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['index']);
        }
        
        return $this->render('edit-comment', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing TimeTracking model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        if (!Yii::$app->user->identity->hasRole('time_tracking_editor')) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }

        $model = $this->findModel($id);

        if ($this->request->isPost && $model->load($this->request->post())) {
            if ($model->save()) {
                // save user activity property
                $properties = ActivityProperty::find()->all();
                foreach ($properties as $property) {
                    $value = Yii::$app->request->post($property->id) ?? null;
                    UserActivityProperty::saveValue($model->user_id, $property->id, $id, $value);
                }
                
                return $this->redirect(['user-statistics', 'user_id' => $model->user_id]);
            }
        }
        
        $activity = Activity::getActivityByUserId($model->user_id, true);

        return $this->render('update', [
            'model' => $model,
            'activity' => $activity
        ]);
    }

    /**
     * Deletes an existing TimeTracking model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        if (!Yii::$app->user->identity->hasRole('time_tracking_editor')) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
        
        $model = $this->findModel($id);
        $user_id = $model->user_id;
        $model->delete();
        
        return $this->redirect(['user-statistics', 'user_id' => $user_id]);
    }

    /**
     * Finds the TimeTracking model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return TimeTracking the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TimeTracking::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
