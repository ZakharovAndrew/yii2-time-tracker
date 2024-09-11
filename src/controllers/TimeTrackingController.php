<?php

namespace ZakharovAndrew\TimeTracker\controllers;

use Yii;
use ZakharovAndrew\TimeTracker\Module;
use ZakharovAndrew\TimeTracker\models\TimeTracking;
use ZakharovAndrew\TimeTracker\models\Activity;
use ZakharovAndrew\user\models\User;
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
       
        return $this->render('index', [
            'user_activity' => $user_activity,
            'allow_statistics' => count(TimeTracking::userRolesForViewingStatistics()) >0
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
            } else {
                Yii::$app->session->setFlash('error', 'Ошибка проставления начала работы');
            }
            
            return $this->redirect('index');
        }
        
        Yii::$app->session->setFlash('error', Modulte::t('The start of work has already been marked'));
        
        return $this->redirect('index');
    }
    
    public function actionBreak()
    {
        $user_activity = TimeTracking::getUserActivity(Yii::$app->user->id);
        
        if (!$user_activity) {
            Yii::$app->session->setFlash('error', Modulte::t("You haven't started work yet!"));

            return $this->redirect('index');
        }
        
        $last_activity = end($user_activity);
        
        if ($last_activity->activity_id == Activity::WORK_STOP) {
            Yii::$app->session->setFlash('error', Module::t("You've already finished your work day!"));

            return $this->redirect('index');
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
        $user_activity = TimeTracking::getUserActivity(Yii::$app->user->id);
        
        if (!$user_activity) {
            Yii::$app->session->setFlash('error', Modulte::t("You haven't started work yet!"));

            return $this->redirect('index');
        }
        
        $last_activity = end($user_activity);
        
        if ($last_activity->activity_id == Activity::WORK_STOP) {
            Yii::$app->session->setFlash('error', Module::t("You've already finished your work day!"));

            return $this->redirect('index');
        }
        
        $model = new TimeTracking([
            'user_id' => Yii::$app->user->id,
            'activity_id' => Activity::WORK_STOP
        ]);
        
        if ($model->save()) {
            Yii::$app->session->setFlash('success', Module::t('The end of the working day is marked'));
        } else {
            Yii::$app->session->setFlash('error', 'Ошибка проставления начала работы');
        }
        
        return $this->redirect('index');
    }
    
    public function actionStatistics($datetime_start = null, $datetime_stop = null, $username = null)
    {
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
        
        if (count($roles) == 0) {
            $model = TimeTracking::find()
                ->leftJoin('users', 'users.id = time_tracking.user_id')
                ->andWhere(['>', 'datetime_at', $start_day])
                ->andWhere(['<=', 'datetime_at', $stop_day])
                //->andWhere('users.id' => $users)
                ->orderBy('datetime_at')->all();
        } else {
            $model = TimeTracking::find()
                ->leftJoin('user_roles', 'user_roles.user_id = time_tracking.user_id')
                ->leftJoin('roles', 'user_roles.role_id = roles.id')
                ->leftJoin('users', 'users.id = time_tracking.user_id')
                ->where(['roles.code' => $roles])
                ->andWhere(['>', 'datetime_at', $start_day])
                ->andWhere(['<=', 'datetime_at', $stop_day])
                //->andWhere('LIKE', 'users.name', $username)
                ->orderBy('datetime_at')->all();
        }
        
        $timeline = [];
        $users = [];
        foreach ($model as $item) {
            $item_name = date('Y-m-d', strtotime($item->datetime_at));
            $timeline[$item_name][$item->user_id][] = $item;
            $users[$item->user_id] = $item->user_id;
        }
        
        return $this->render('statistics', [
            'timeline' => $timeline,
            'datetime_start' => $datetime_start,
            'datetime_stop' => $datetime_stop,
            'activities' => Activity::getList(),
            'users' => ArrayHelper::map(
                        \ZakharovAndrew\user\models\User::find()
                            ->where(['id' => $users])
                            ->orderBy('name')
                            ->all(),
                        'id', 'name'
                    )
        ]);
    }
    
    public function actionUserStatistics($user_id = null)
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
                ->andWhere(['>', 'datetime_at', date('Y-m-d 00:00:00', strtotime('-7 days'))])
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
        
        $user_activity = TimeTracking::getUserActivity(Yii::$app->user->id);
        
        if (!$user_activity) {
            Yii::$app->session->setFlash('error', Modulte::t("You haven't started work yet!"));

            return $this->redirect('index');
        }
        
        $model = new TimeTracking();
        
        $model->user_id = Yii::$app->user->id;

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                return $this->redirect(['index']);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }
    
    public function actionAdd()
    {
        if (!Yii::$app->user->identity->hasRole('time_tracking_editor')) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
        
        
        $model = new TimeTracking();
        
        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                Yii::$app->session->setFlash('success', 'Активность добавлена');
                return $this->redirect(['user-statistics', 'user_id' => $model->user_id]);
            }
        } 
        
        Yii::$app->session->setFlash('error', 'Ошибка при добавление активности пользователю');
        
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
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
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
