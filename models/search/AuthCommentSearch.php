<?php

namespace app\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\AuthComment;

/**
 * AuthCommentSearch represents the model behind the search form of `app\models\AuthComment`.
 */
class AuthCommentSearch extends AuthComment
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'auth_app_id', 'auth_account_id', 'auth_item_id', 'auth_uid', 'parent_id', 'is_top', 'digg_count', 'reply_count', 'create_time', 'post_time'], 'integer'],
            [['comment_id', 'content'], 'safe'],
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
        $query = AuthComment::find();

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
            'auth_app_id' => $this->auth_app_id,
            'auth_account_id' => $this->auth_account_id,
            'auth_item_id' => $this->auth_item_id,
            'auth_uid' => $this->auth_uid,
            'parent_id' => $this->parent_id,
            'is_top' => $this->is_top,
            'digg_count' => $this->digg_count,
            'reply_count' => $this->reply_count,
            'create_time' => $this->create_time,
            'post_time' => $this->post_time,
        ]);

        $query->andFilterWhere(['like', 'comment_id', $this->comment_id])
            ->andFilterWhere(['like', 'content', $this->content]);

        return $dataProvider;
    }
}
