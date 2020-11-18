<?php

namespace app\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\AuthItem;

/**
 * AuthItemSearch represents the model behind the search form of `app\models\AuthItem`.
 */
class AuthItemSearch extends AuthItem
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'auth_app_id', 'auth_account_id', 'video_id', 'is_top', 'comment_count', 'digg_count', 'download_count', 'play_count', 'share_count', 'forward_count', 'timing', 'post_time', 'create_time'], 'integer'],
            [['item_id', 'title', 'cover'], 'safe'],
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
        $query = AuthItem::find();

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
            'video_id' => $this->video_id,
            'is_top' => $this->is_top,
            'comment_count' => $this->comment_count,
            'digg_count' => $this->digg_count,
            'download_count' => $this->download_count,
            'play_count' => $this->play_count,
            'share_count' => $this->share_count,
            'forward_count' => $this->forward_count,
            'timing' => $this->timing,
            'post_time' => $this->post_time,
            'create_time' => $this->create_time,
        ]);

        $query->andFilterWhere(['like', 'item_id', $this->item_id])
            ->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'cover', $this->cover]);
        if(!isset($params['sort'])){
            $query->orderBy('post_time desc');
        }

        return $dataProvider;
    }
}
