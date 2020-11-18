<?php

namespace app\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\AuthAccount;

/**
 * AuthAccountSearch represents the model behind the search form of `app\models\AuthAccount`.
 */
class AuthAccountSearch extends AuthAccount
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'uid', 'auth_app_id', 'app_uid', 'token_expire_time', 'refresh_token_expire_time', 'refresh_token_get_nums', 'all_expire_time', 'ctime'], 'integer'],
            [['open_id', 'nickname', 'avatar', 'token', 'refresh_token'], 'safe'],
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
        $query = AuthAccount::find();

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
            'uid' => $this->uid,
            'auth_app_id' => $this->auth_app_id,
            'app_uid' => $this->app_uid,
            'token_expire_time' => $this->token_expire_time,
            'refresh_token_expire_time' => $this->refresh_token_expire_time,
            'refresh_token_get_nums' => $this->refresh_token_get_nums,
            'all_expire_time' => $this->all_expire_time,
            'ctime' => $this->ctime,
        ]);

        $query->andFilterWhere(['like', 'open_id', $this->open_id])
            ->andFilterWhere(['like', 'nickname', $this->nickname])
            ->andFilterWhere(['like', 'avatar', $this->avatar])
            ->andFilterWhere(['like', 'token', $this->token])
            ->andFilterWhere(['like', 'refresh_token', $this->refresh_token]);

        return $dataProvider;
    }
}
