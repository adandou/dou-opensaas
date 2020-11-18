<?php

namespace app\modules\weixin\controllers;

use app\modules\weixin\WeixinController;
use yii\web\Controller;

/**
 * Default controller for the `weixin` module
 */
class DefaultController extends WeixinController
{
    public $oauth_scope = 0;
    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }
}
