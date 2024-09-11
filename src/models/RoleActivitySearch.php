<?php

namespace ZakharovAndrew\TimeTracker\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use ZakharovAndrew\TimeTracker\models\RoleActivity;

/**
 * RoleActivitySearch represents the model behind the search form of `ZakharovAndrew\TimeTracker\models\RoleActivity`.
 */
class RoleActivitySearch extends RoleActivity
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'role_id', 'activity_id', 'pos'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params, $role_id)
    {
        $query = RoleActivity::find()
                ->select('time_tracking_role_activity.*, time_tracking_activity.name as activity_title')
                ->leftJoin('time_tracking_activity', 'time_tracking_activity.id = time_tracking_role_activity.activity_id');

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'role_id' => $role_id,
            'activity_id' => $this->activity_id,
            'pos' => $this->pos,
        ]);

        return $dataProvider;
    }
    
    public function searchList($params)
    {
        $query = RoleActivity::find()
                ->select('roles.id as role_id, roles.title as role_title')
                ->rightJoin('roles', 'roles.id = time_tracking_role_activity.role_id')
                ->groupBy('roles.id');

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'roles.id' => $this->role_id,
            /*'activity_id' => $this->activity_id,
            'pos' => $this->pos,*/
        ]);

        return $dataProvider;
    }
}
