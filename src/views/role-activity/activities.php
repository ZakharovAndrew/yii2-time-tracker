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

$this->title = Module::t('Activities');
$this->params['breadcrumbs'][] = ['label' => Module::t('Role Activities'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="role-activity-index">

    <?php if (Yii::$app->getModule('timetracker')->showTitle) {?><h1><?= Html::encode($this->title) ?></h1><?php } ?>
    
    <p>
        <?= Html::a(Module::t('Add Activity'), ['add', 'role_id' => $role_id], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [

            [
                'attribute' => 'activity_id',
                'label' => Module::t('Activity'),
                'filter' => ZakharovAndrew\TimeTracker\models\Activity::getDropdownList(),
                'filterInputOptions' => ['class' => 'form-control form-select'],
                'value'  => function ($model) {
                    return $model->activity_title;
                }
            ],
            'pos',
            [
                'class' => ActionColumn::className(),
                'urlCreator' => function ($action, $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                 }
            ],
        ],
    ]); ?>


</div>
