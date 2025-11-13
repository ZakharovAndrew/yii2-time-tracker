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
        $top_month_activities = TimeTracking::find()->alias('t')
                ->select(['t.activity_id', 'a.name', 'cnt' => 'count(*)', 'duration' => 'sum(duration)'])
                ->leftJoin(Activity::tableName().' a', 'a.id = t.activity_id')
                ->where(['NOT IN', 't.activity_id', [Activity::WORK_START, Activity::WORK_STOP]])
                ->andWhere(['>', 'datetime_at', date('Y-m-d', strtotime('-1 month'))])
                ->groupBy('activity_id')
                ->asArray()
                ->orderBy('cnt DESC')
                ->limit(10)
                ->all();
        
        $top_week_activities = TimeTracking::find()->alias('t')
                ->select(['t.activity_id', 'a.name', 'cnt' => 'count(*)', 'duration' => 'sum(duration)'])
                ->leftJoin(Activity::tableName().' a', 'a.id = t.activity_id')
                ->where(['NOT IN', 't.activity_id', [Activity::WORK_START, Activity::WORK_STOP]])
                ->andWhere(['>', 'datetime_at', date('Y-m-d', strtotime('-7 days'))])
                ->groupBy('activity_id')
                ->asArray()
                ->orderBy('cnt DESC')
                ->limit(10)
                ->all();
        
        $top_day_activities = TimeTracking::find()->alias('t')
                ->select(['t.activity_id', 'a.name', 'cnt' => 'count(*)'])
                ->leftJoin(Activity::tableName().' a', 'a.id = t.activity_id')
                ->where(['<>', 't.activity_id', Activity::WORK_START])
                ->where(['>', 'datetime_at', date('Y-m-d')])
                ->groupBy('activity_id')
                ->asArray()
                ->orderBy('cnt DESC')
                ->limit(10)
                ->all();
            
        $data = [
            'count_all_activities' => TimeTracking::find()->count(),
            'coun_last_month_activities' => TimeTracking::find()->where(['>', 'datetime_at', date('Y-m-d', strtotime('-1 month'))])->count(),
            'coun_last_week_activities' => TimeTracking::find()->where(['>', 'datetime_at', date('Y-m-d', strtotime('-7 days'))])->count(),
            'top_month_activities' => $top_month_activities,
            'top_week_activities' => $top_week_activities,
            'top_today_activities' => $top_day_activities
        ];
        
        return $this->render('index', [
            'data' => $data
        ]);
    }
}
