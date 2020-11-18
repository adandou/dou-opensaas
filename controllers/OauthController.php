<?php

namespace app\controllers;

use app\models\auth_app\AuthAppDouyin;
use app\models\AuthAccount;
use app\models\AuthApp;
use app\models\AuthAppMsg;
use app\models\AuthUser;
use app\models\DouyinAccount;
use app\models\DouyinMsg;
use app\models\DouyinOauthUser;
use app\models\DouyinUser;
use Yii;
use yii\web\Controller;


class OauthController extends Controller
{
    public function actionDouyin($code,$state){
        $appBase = AuthApp::findOne($state);
        $app = $appBase->getAppObj();
        $app->initToken($code);

        return $this->redirect('/agent/');
    }
    public function actionMsg($id){
        $data = file_get_contents('php://input');
        $authApp = AuthApp::findOne($id);
        if(empty($authApp)) return ;
        $authAppMsg = $authApp->getAppObj()->getMsgObj($data);
        if(empty($authAppMsg)) return ;
        return $authAppMsg->run();
    }
    public function actionToutiao($code,$state){
        $appBase = AuthApp::findOne($state);
        $app = $appBase->getAppObj();
        $app->initToken($code);

        return $this->redirect('/agent/');
    }
    public function init(){
        parent::init();
//        set_exception_handler([$this, 'myException']);
    }
    //自定义异常捕获
    public function myException(\Throwable $e){
        echo $e->getFile().":".$e->getLine().":".$e->getMessage();
        exit;
    }
}
