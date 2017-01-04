<?php

namespace app\models\table;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\table\SourceAgregate;

/**
 * SourceAgregateSearch represents the model behind the search form about `app\models\table\SourceAgregate`.
 */
class SourceAgregateSearch extends SourceAgregate
{
    public $source;
    public $startDate;
    public $endDate;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'id_source', 'conversion', 'id_user', 'update_manual'], 'integer'],
            [['date', 'date_create'], 'safe'],
            [['spend', 'return', 'return_amsterdam'], 'number'],
            [['source'], 'safe'],
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
        $query = SourceAgregate::find();
        $query->joinWith(['source'])
            ->where(['id_user' => Yii::$app->user->id]);
        if( isset($this->startDate) ){
            $query->andFilterWhere(['between', 'date', $this->startDate, $this->endDate]);
        };
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
        $this->load($params);
        if (!$this->validate()) {
            return $dataProvider;
        }
        $query->andFilterWhere([
            'id' => $this->id,
            'id_source' => $this->id_source
        ]);
        $query->andFilterWhere(['like', 'date', $this->date])
            ->andFilterWhere(['like', 'date_create', $this->date_create])
            ->andFilterWhere(['like', 'spend', $this->spend])
            ->andFilterWhere(['like', 'return', $this->return])
            ->andFilterWhere(['like', 'return_amsterdam', $this->return_amsterdam])
            ->andFilterWhere(['like', 'source.name', $this->source])
            ->andFilterWhere(['like', 'conversion', $this->conversion])
            ->orderBy([
                'date' => SORT_ASC,
                'source.name' => SORT_ASC,
            ]);
        return $dataProvider;
    }
}
