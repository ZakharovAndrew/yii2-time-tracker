<?php

namespace ZakharovAndrew\TimeTracker\controllers;

use ZakharovAndrew\TimeTracker\models\Activity;
use ZakharovAndrew\TimeTracker\models\TimeTracking;
use ZakharovAndrew\user\controllers\ParentController;
use yii\helpers\ArrayHelper;

class DashboardController extends ParentController
{
    public $controller_id = 5005;

    /**
     * Dashboard.
     *
     * @return string
     */
    public function actionIndex()
    {
        $now = date('Y-m-d H:i:s');
        $today = date('Y-m-d');
        $weekAgo = date('Y-m-d', strtotime('-7 days'));
        $monthAgo = date('Y-m-d', strtotime('-1 month'));
        
        $cacheKey = "dashboard_data_" . date('Y-m-d-H');
        $data = \Yii::$app->cache->getOrSet($cacheKey, function() use ($today, $weekAgo, $monthAgo) {
            return [
                'count_all_activities' => TimeTracking::find()->count(),
                'count_last_month_activities' => TimeTracking::getCountFromDate($monthAgo),
                'count_last_week_activities' => TimeTracking::getCountFromDate($weekAgo),
                'top_month_activities' => TimeTracking::getTopActivitiesQuery($monthAgo)->asArray()->all(),
                'top_week_activities' => TimeTracking::getTopActivitiesQuery($weekAgo)->asArray()->all(),
                'top_today_activities' => TimeTracking::getTopActivitiesQuery($today)->asArray()->all(),
            ];
        }, 300); // 5 min cache

        return $this->render('index', [
            'data' => $data
        ]);
    }
    
    public function actionDetail($period, $activity_id)
    {
        $model = $this->findModel($activity_id);
        
        $start_date = date('Y-m-d');
        if ($period == 'month') {
            $start_date = date('Y-m-d', strtotime('-1 month'));
        } else if ($period == 'week') {
            $start_date = date('Y-m-d', strtotime('-7 days'));
        }
        
        $data = TimeTracking::find()->alias('t')
                ->select(['t.user_id', 'u.name', 'cnt' => 'count(*)', 'duration' => 'sum(duration)'])
                ->leftJoin(Activity::tableName().' a', 'a.id = t.activity_id')
                ->leftJoin(\app\models\User::tableName().' u', 'u.id = t.user_id')
                ->where(['NOT IN', 't.activity_id', [Activity::WORK_START, Activity::WORK_STOP, Activity::WORK_BREAK]])
                ->andWhere(['>', 'datetime_at', $start_date])
                ->andWhere(['activity_id' => $activity_id])
                ->groupBy('t.user_id')
                ->asArray()
                ->orderBy('cnt DESC, duration DESC');
        
        return $this->render('detail', [
            'data' => $data->all(),
            'model' => $model
        ]);
    }
    
    /**
     * Finds the Activity model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return Activity the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Activity::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
