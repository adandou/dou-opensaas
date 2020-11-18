<?php

namespace app\modules\weixin\controllers;

use app\modules\weixin\WeixinController;
use yii\web\Controller;

/**
 * Default controller for the `weixin` module
 */
class MyController extends WeixinController
{
    public $oauth_scope = 0;
    /**
     * 积分榜
     */
    public function actionIndex()
    {
        return $this->render('index',['objs'=>[]]);
    }
}
