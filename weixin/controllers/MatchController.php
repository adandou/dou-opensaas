<?php

namespace app\modules\weixin\controllers;

use app\models\MatchData;
use app\models\Matchs;
use app\modules\weixin\WeixinController;
use yii\web\Controller;

/**
 * Default controller for the `weixin` module
 */
class MatchController extends WeixinController
{
    public $oauth_scope = 0;
    /**
     * 积分榜
     */
    public function actionData($match_id)
    {
        try{
            $match = Matchs::findOne($match_id);
            if(empty($match))throw new \Exception('比赛不存在', 1);
            $teams = [
                $match->team_1_id => $match->team1->title,
                $match->team_2_id => $match->team2->title,
            ];
            $objs = MatchData::find()->where(['match_id'=>$match_id])->all();
            $rows = [];
            foreach ($objs as $obj) {
                $rows[] = $obj->getListData();
            }
//            print_r($objs);
            return $this->render('data',[
                'game_id'=>$match->game_id,
                'match_id'=>$match_id,
                'teams'=>$teams,
                'types'=>MatchData::$types,
                'datas'=>$objs,
                ]);
        }catch (\Exception $e){
            return $this->error($e);
        }
    }
}
