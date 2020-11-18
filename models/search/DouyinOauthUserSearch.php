<?php

namespace app\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\DouyinOauthUser;

/**
 * DouyinOauthUserSearch represents the model behind the search form of `app\models\DouyinOauthUser`.
 */
class DouyinOauthUserSearch extends DouyinOauthUser
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'douyin_app_id', 'douyin_uid', 'access_expire', 'refresh_expire'], 'integer'],
            [['open_id', 'access_token', 'refresh_token'], 'safe'],
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
        $query = DouyinOauthUser::find();

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
            'douyin_app_id' => $this->douyin_app_id,
            'douyin_uid' => $this->douyin_uid,
            'access_expire' => $this->access_expire,
            'refresh_expire' => $this->refresh_expire,
        ]);

        $query->andFilterWhere(['like', 'open_id', $this->open_id])
            ->andFilterWhere(['like', 'access_token', $this->access_token])
            ->andFilterWhere(['like', 'refresh_token', $this->refresh_token]);

        return $dataProvider;
    }
}
