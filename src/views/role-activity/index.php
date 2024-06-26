<?php

use ZakharovAndrew\TimeTracker\models\RoleActivity;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use ZakharovAndrew\user\models\Roles;
use ZakharovAndrew\TimeTracker\Module;

/** @var yii\web\View $this */
/** @var app\models\RoleActivitySearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = Module::t('Role Activities');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="role-activity-index">

    <?php if (Yii::$app->getModule('timetracker')->showTitle) {?><h1><?= Html::encode($this->title) ?></h1><?php } ?>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [

            [
                'attribute' => 'role_id',
                'label' => Module::t('Role'),
                'filter' => Roles::getRolesList(),
                'filterInputOptions' => ['class' => 'form-control form-select'],
                'value'  => function ($model) {
                    return $model->role_title;
                }
            ],
            [
                'label' => Module::t('Activity'),
                'value'  => function ($model) {
                    $list = $model->getActivities();
                    return implode(', ', $list);
                }
            ],
            [
                'format' => 'raw',
                'value'  => function ($model) {
                    return Html::a( Module::t('Edit'), ['activities', 'role_id' => $model->role_id], ['title' =>  Module::t('Edit')]);
                }
            ],
        ],
    ]); ?>


</div>
