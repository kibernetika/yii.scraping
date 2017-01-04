<?php

namespace app\models\table;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\table\SourceReturn;

/**
 * SourceReturnSearch represents the model behind the search form about `app\models\table\SourceReturn`.
 */
class SourceReturnSearch extends SourceReturn
{
    public $source;
    public $user;
    public $startDate;
    public $endDate;
    public $total;
    public $average;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'id_source', 'conversion', 'id_user'], 'integer'],
            [['date', 'date_create'], 'safe'],
            [['return'], 'number'],
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
        $query = SourceReturn::find();
        $query->joinWith(['source']);
        $query->joinWith(['user']);
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
            $this->total = 0;
            foreach ( $query->all() as $record ){
                $this->total += $record['return'];
            }
            if ( $query->count() == 0 ){
                $this->average = 0;
            }else{
                $this->average = $this->total / $query->count();
            }
            return $dataProvider;
        }
        $query->andFilterWhere([
            'id' => $this->id,
            'id_source' => $this->id_source,
        ]);
        $query->andFilterWhere([
            'id' => $this->id,
            'id_user' => $this->id_user,
        ]);
        $query
            ->andFilterWhere(['like', 'date', $this->date])
            ->andFilterWhere(['like', 'date_create', $this->date_create])
            ->andFilterWhere(['like', 'return', $this->return])
            ->andFilterWhere(['like', 'conversion', $this->conversion])
            ->andFilterWhere(['like', 'source.name', $this->source])
            ->andFilterWhere(['like', 'user.name', $this->user])
            ->orderBy([
                'date' => SORT_ASC,
                'source.name' => SORT_ASC,
            ]);
        $this->total = 0;
        foreach ( $query->all() as $record ){
            $this->total += $record['return'];
        }
        if ( $query->count() == 0 ){
            $this->average = 0;
        }else{
            $this->average = $this->total / $query->count();
        }
        return $dataProvider;
    }
}
