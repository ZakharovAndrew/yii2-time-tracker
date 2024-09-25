<?php

namespace ZakharovAndrew\TimeTracker\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use ZakharovAndrew\TimeTracker\models\ActivityProperty;

/**
 * ActivityPropertySearch represents the model behind the search form of `ZakharovAndrew\TimeTracker\models\ActivityProperty`.
 */
class ActivityPropertySearch extends ActivityProperty
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'type', 'pos'], 'integer'],
            [['name', 'values'], 'safe'],
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
    public function search($params)
    {
        $query = ActivityProperty::find();

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
            'type' => $this->type,
            'pos' => $this->pos,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'values', $this->values]);

        return $dataProvider;
    }
}
