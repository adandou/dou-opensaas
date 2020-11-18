<?php

namespace app\modules\weixin\controllers;

use app\modules\weixin\WeixinController;
use yii\web\Controller;

/**
 * Default controller for the `weixin` module
 */
class GameController extends WeixinController
{
    public $oauth_scope = 0;
    /**
     * 积分榜
     */
    public function actionScore()
    {
        return $this->render('score',['objs'=>[]]);
    }
}
