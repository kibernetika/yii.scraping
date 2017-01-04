<?php

namespace app\models\table;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\table\Balance;

/**
 * BalanceSearch represents the model behind the search form about `app\models\table\Balance`.
 */
class BalanceSearch extends Balance
{
    public $source;
    public $user;
    public $startDate;
    public $endDate;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'id_source', 'id_user'], 'integer'],
            [['date', 'date_create'], 'safe'],
            [['money'], 'number'],
            [['source'], 'safe'],
            [['user'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
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
        $query = Balance::find();
        $query->joinWith(['user']);
        $query->joinWith(['source']);
        if( isset($this->startDate) ){
            $query->andFilterWhere(['between', 'date', $this->startDate, $this->endDate]);
        }
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 31,
            ],
        ]);
        $dataProvider->sort->attributes['source'] = [
            'asc' => ['source.name' => SORT_ASC],
            'desc' => ['source.name' => SORT_DESC],
        ];
        $dataProvider->sort->attributes['user'] = [
            'asc' => ['user.name' => SORT_ASC],
            'desc' => ['user.name' => SORT_DESC],
        ];
        $this->load($params);
        if (!$this->validate()) {
            return $dataProvider;
        }
        $query->andFilterWhere([
            'id' => $this->id,
            'id_user' => $this->id_user
        ]);
        $query->andFilterWhere([
            'id' => $this->id,
            'id_source' => $this->id_source
        ]);
        $query->andFilterWhere(['like', 'date', $this->date])
            ->andFilterWhere(['like', 'date_create', $this->date_create])
            ->andFilterWhere(['like', 'money', $this->money])
            ->andFilterWhere(['like', 'source.name', $this->source])
            ->andFilterWhere(['like', 'user.name', $this->user])
            ->orderBy([
                'date' => SORT_ASC,
                'source.name' => SORT_ASC,
            ]);
        return $dataProvider;
    }
}
