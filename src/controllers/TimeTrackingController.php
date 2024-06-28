<?php

namespace ZakharovAndrew\TimeTracker\controllers;

use Yii;
use ZakharovAndrew\TimeTracker\Module;
use ZakharovAndrew\TimeTracker\models\TimeTracking;
use ZakharovAndrew\TimeTracker\models\Activity;
use ZakharovAndrew\user\controllers\ParentController;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * TimeTrackingController implements the CRUD actions for TimeTracking model.
 * @author Andrew Zakharov https://github.com/ZakharovAndrew
 */
class TimeTrackingController extends ParentController
{
    
    public $controller_id = 5001;
    
    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [
                'verbs' => [
                    'class' => VerbFilter::className(),
                    'actions' => [
                        'delete' => ['POST'],
                    ],
                ],
            ]
        );
    }

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
        ]);
    }
    
    public function actionStart()
    {
        $user_activity = TimeTracking::getUserActivity(Yii::$app->user->id);
        
        if (!$user_activity) {
            $model = new TimeTracking([
                'user_id' => Yii::$app->user->id,
                'activity_id' => Activity::START_DAY
            ]);
            
            if ($model->save()) {
                Yii::$app->session->setFlash('success', Module::t('The start of the working day is marked'));
            } else {
                Yii::$app->session->setFlash('error', 'Ошибка проставления начала работы');
            }
            
            return $this->redirect('index');
        }
        
        Yii::$app->session->setFlash('error', 'Начало работы уже проставлено');
        
        return $this->redirect('index');
        
    }
    
    public function actionStop()
    {
        $user_activity = TimeTracking::getUserActivity(Yii::$app->user->id);
        
        if (!$user_activity) {
            Yii::$app->session->setFlash('error', 'Вы еще не начали работу!');

            return $this->redirect('index');
        }
        
        $last_activity = end($user_activity);
        
        if ($last_activity->activity_id == Activity::STOP_DAY) {
            Yii::$app->session->setFlash('error', Module::t("You've already finished your work day!"));

            return $this->redirect('index');
        }
        
        $model = new TimeTracking([
            'user_id' => Yii::$app->user->id,
            'activity_id' => Activity::STOP_DAY
        ]);
        
        if ($model->save()) {
            Yii::$app->session->setFlash('success', Module::t('The end of the working day is marked'));
        } else {
            Yii::$app->session->setFlash('error', 'Ошибка проставления начала работы');
        }
        
        return $this->redirect('index');
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

    /**
     * Updates an existing TimeTracking model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
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
