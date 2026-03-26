<?php

namespace ZakharovAndrew\TimeTracker\controllers;

use Yii;
use ZakharovAndrew\TimeTracker\models\Activity;
use ZakharovAndrew\TimeTracker\models\TimeTracking;
use ZakharovAndrew\user\controllers\ParentController;
use yii\helpers\ArrayHelper;
use ZakharovAndrew\user\models\UserSettingsConfig;

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
                'count_today_activities' => TimeTracking::getCountFromDate($today),
                'top_month_activities' => TimeTracking::getTopActivitiesQuery($monthAgo)->asArray()->all(),
                'top_week_activities' => TimeTracking::getTopActivitiesQuery($weekAgo)->asArray()->all(),
                'top_today_activities' => TimeTracking::getTopActivitiesQuery($today)->asArray()->all(),
            ];
        }, 300); // 5 min cache

        return $this->render('index', [
            'data' => $data
        ]);
    }
    
    public function actionDetail($period, $activity_id, $order_by = 'count', $date = null)
    {
        $model = $this->findModel($activity_id);
        
        $start_date = date('Y-m-d');
        if ($period == 'month') {
            $start_date = date('Y-m-d', strtotime('-1 month'));
            if (!empty($date)) {
                $start_date = date('Y-m-01', strtotime($date));
                $stop_date = date('Y-m-01', strtotime($start_date. ' + 1 month'));
            }
        } else if ($period == 'week') {
            $start_date = date('Y-m-d', strtotime('-7 days'));
        }
        
        $order_by_sql = ($order_by == 'duration' ? 'duration DESC, cnt DESC' : 'cnt DESC, duration DESC');
        
        $data = TimeTracking::find()->alias('t')
                ->select(['t.user_id', 'u.name', 'cnt' => 'count(*)', 'duration' => 'sum(duration)'])
                ->leftJoin(Activity::tableName().' a', 'a.id = t.activity_id')
                ->leftJoin(\app\models\User::tableName().' u', 'u.id = t.user_id')
                ->where(['NOT IN', 't.activity_id', [Activity::WORK_START, Activity::WORK_STOP, Activity::WORK_BREAK]])
                ->andWhere(['>', 'datetime_at', $start_date])
                ->andWhere(['activity_id' => $activity_id])
                ->groupBy('t.user_id')
                ->asArray()
                ->orderBy($order_by_sql);
        
        if (isset($stop_date)) {
            $data->andWhere(['<', 'datetime_at', $stop_date]);
        }
        
        return $this->render('detail', [
            'data' => $data->all(),
            'model' => $model,
            'period' => $period,
            'activity_id' => $activity_id,
            'order_by' => $order_by,
            'user_properties_column' => UserSettingsConfig::find()->where([
                'code' => Yii::$app->getModule('timetracker')->dashboardUserProperties
            ])->all()
        ]);
    }
    
    public function actionActivity($id)
    {
        $model = $this->findModel($id);
        
        $data = TimeTracking::find()->alias('t')
            ->select([
                'month' => "DATE_FORMAT(t.datetime_at, '%Y-%m')", // Группировка по месяцам (формат: 2024-01)
                'month_name' => "DATE_FORMAT(t.datetime_at, '%M %Y')",
                'cnt' => 'COUNT(*)', // Количество уникальных активностей
                'activity_count' => 'COUNT(*)', // Общее количество записей
                'duration' => 'SUM(t.duration)'
            ])
            ->where(['NOT IN', 't.activity_id', [Activity::WORK_START, Activity::WORK_STOP, Activity::WORK_BREAK]])
            ->andWhere(['>', 't.datetime_at', date('Y-m-01', strtotime('-6 month'))])
            ->andWhere(['t.activity_id' => $id])
            ->groupBy(['month']) // Группируем по пользователю и месяцу
            ->orderBy('month ASC')
            ->asArray()
            ->all();
                
        return $this->render('activity', [
            'data' => $data,
            'model' => $model,
        ]);
    }
    
    public function actionActivityProperty($period = null, $order_by = 'count')
    {        
        $start_date = date('Y-m-d');
        if ($period == 'month') {
            $start_date = date('Y-m-d', strtotime('-1 month'));
            if (!empty($date)) {
                $start_date = date('Y-m-01', strtotime($date));
                $stop_date = date('Y-m-01', strtotime($start_date. ' + 1 month'));
            }
        } else if ($period == 'week') {
            $start_date = date('Y-m-d', strtotime('-7 days'));
        }
        
        $order_by_sql = ($order_by == 'duration' ? 'duration DESC, cnt DESC' : 'cnt DESC, duration DESC');
        
        $data = TimeTracking::find()->alias('t')
                ->select(['t.activity_id', 'a.name', 'cnt' => 'count(*)', 'values' => "ifnull(p.values, '')", 'duration' => 'sum(duration)'])
                ->leftJoin(Activity::tableName().' a', 'a.id = t.activity_id')
                ->leftJoin(\ZakharovAndrew\TimeTracker\models\UserActivityProperty::tableName().' p', 'p.activity_id = t.id')
                ->where(['NOT IN', 't.activity_id', [Activity::WORK_START, Activity::WORK_STOP, Activity::WORK_BREAK]])
                ->andWhere(['>', 'datetime_at', $start_date])
                ->groupBy(["t.activity_id", "ifnull(p.values, '')"])
                ->asArray()
                ->orderBy($order_by_sql); 
        
        return $this->render('activity-property', [
            'data' => $data->all(),
            'period' => $period,
            'order_by' => $order_by
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
