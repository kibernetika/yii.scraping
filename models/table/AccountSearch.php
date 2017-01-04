<?php

namespace app\models\table;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * AccountSearch represents the model behind the search form about `app\models\table\Account`.
 */
class AccountSearch extends Account
{
    public $source;
    public $users;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'id_source', 'id_user'], 'integer'],
            [['login', 'pass', 'api_first_key', 'api_second_key'], 'safe'],
            [['source', 'users'], 'safe'],
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
        $query = Account::find();
        $query->joinWith(['users', 'source']);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        $dataProvider->sort->attributes['source'] = [
            'asc' => ['source.name' => SORT_ASC],
            'desc' => ['source.name' => SORT_DESC],
        ];
        $dataProvider->sort->attributes['users'] = [
            'asc' => ['users.name' => SORT_ASC],
            'desc' => ['users.name' => SORT_DESC],
        ];
        $this->load($params);
        if (!$this->validate()) {
            return $dataProvider;
        }
        $query->andFilterWhere([
            'id' => $this->id,
            'id_source' => $this->id_source,
            'id_user' => $this->id_user
        ]);
        $query->andFilterWhere(['like', 'login', $this->login])
            ->andFilterWhere(['like', 'pass', $this->pass])
            ->andFilterWhere(['like', 'api_first_key', $this->api_first_key])
            ->andFilterWhere(['like', 'api_second_key', $this->api_second_key])
            ->andFilterWhere(['like', 'source.name', $this->source])
            ->andFilterWhere(['like', 'users.name', $this->users])
            ->orderBy(['source.name' => SORT_ASC]);;
        return $dataProvider;
    }
}
