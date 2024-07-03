<?php

namespace ZakharovAndrew\TimeTracker\controllers;

use ZakharovAndrew\TimeTracker\models\RoleActivity;
use ZakharovAndrew\TimeTracker\models\RoleActivitySearch;
use ZakharovAndrew\user\controllers\ParentController;
use ZakharovAndrew\user\models\Roles;
use yii\web\NotFoundHttpException;

/**
 * RoleActivityController implements the CRUD actions for RoleActivity model.
 */
class RoleActivityController extends ParentController
{

    /**
     * Lists all RoleActivity models by role_id.
     *
     * @return string
     */
    public function actionActivities($role_id)
    {
        $searchModel = new RoleActivitySearch();
        $dataProvider = $searchModel->search($this->request->queryParams, $role_id);

        return $this->render('activities', [
            'role_id' => $role_id,
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
    
    /**
     * Creates a new RoleActivity model.
     * If creation is successful, the browser will be redirected to the 'activities' page.
     * @return string|\yii\web\Response
     */
    public function actionAdd($role_id)
    {
        $model = new RoleActivity();

        if ($this->request->isPost) {
            if ($model->load($this->request->post())) {
                $model->role_id = $role_id;
                if ($model->save()) {
                    return $this->redirect(['activities', 'role_id' => $role_id]);
                }
            }
        } else {
            $model->loadDefaultValues();
            $model->role_id = $role_id;
        }

        return $this->render('add', [
            'model' => $model,
        ]);
    }
    
    /**
     * Lists all RoleActivity models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new RoleActivitySearch();
        $dataProvider = $searchModel->searchList($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single RoleActivity model.
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
     * Creates a new RoleActivity model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new RoleActivity();

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing RoleActivity model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['activities', 'role_id' => $model->role_id]);
        }

        return $this->render('update', [
            'model' => $model,
            'role' => Roles::findOne($model->role_id)
        ]);
    }

    /**
     * Deletes an existing RoleActivity model.
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
     * Finds the RoleActivity model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return RoleActivity the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = RoleActivity::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
