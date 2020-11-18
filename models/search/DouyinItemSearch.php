<?php

namespace app\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\DouyinItem;

/**
 * DouyinItemSearch represents the model behind the search form of `app\models\DouyinItem`.
 */
class DouyinItemSearch extends DouyinItem
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'type', 'is_top', 'comment_count', 'digg_count', 'download_count', 'play_count', 'share_count', 'forward_count', 'item_state', 'create_time', 'post_time'], 'integer'],
            [['item_id', 'title', 'cover', 'item_data'], 'safe'],
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
        $query = DouyinItem::find();

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
            'is_top' => $this->is_top,
            'comment_count' => $this->comment_count,
            'digg_count' => $this->digg_count,
            'download_count' => $this->download_count,
            'play_count' => $this->play_count,
            'share_count' => $this->share_count,
            'forward_count' => $this->forward_count,
            'item_state' => $this->item_state,
            'create_time' => $this->create_time,
            'post_time' => $this->post_time,
        ]);

        $query->andFilterWhere(['like', 'item_id', $this->item_id])
            ->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'cover', $this->cover])
            ->andFilterWhere(['like', 'item_data', $this->item_data]);
        $query->orderBy('create_time desc');
        return $dataProvider;
    }
}
