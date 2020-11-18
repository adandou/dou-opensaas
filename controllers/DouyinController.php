<?php

namespace app\controllers;

use app\models\DouyinAccount;
use app\models\DouyinApp;
use app\models\DouyinMsg;
use app\models\DouyinOauthUser;
use app\models\DouyinUser;
use Yii;
use yii\web\Controller;


class DouyinController extends Controller
{
    public function actionOauth($code,$state){
        $douyinApp = DouyinApp::findOne(1);
        $token_arr = $douyinApp->getTokenByCode($code);

        $obj = DouyinAccount::findOne(['open_id'=>$token_arr['open_id']]);
        if(empty($obj)){
            $obj = new DouyinAccount();
            $obj->douyin_app_id = $douyinApp->id;
            $obj->open_id = $token_arr['open_id'];
        }
        $obj->wx_uid = Yii::$app->user->id;
        $obj->access_token = $token_arr['access_token'];
        $obj->access_expire = (time() + $token_arr['access_expire']);
        $obj->refresh_token = $token_arr['refresh_token'];
        $obj->refresh_expire = (time() + $token_arr['refresh_expire']);
        if(!$obj->save()){
            throw new \Exception(json_encode($obj->getErrors(),JSON_UNESCAPED_UNICODE));
        }
        $douyinUser = DouyinUser::find()->where(['open_id'=>$obj->open_id])->one();
        if(empty($douyinUser)){
            $douyinUser = new DouyinUser();
            $douyinUser->douyin_account_id = $obj->id;
            $douyinUser->open_id = $obj->open_id;
            $douyinUser->updateByDouyin();

        }
        $obj->union_id = $douyinUser->union_id;
        $obj->nickname = $douyinUser->nickname;
        $obj->avatar = $douyinUser->avatar;
        $obj->douyin_uid = $douyinUser->id;
        if(!$obj->save()){
            throw new \Exception(json_encode($obj->getErrors(),JSON_UNESCAPED_UNICODE));
        }
        return $this->redirect('/agent/');
    }
    //webhooks
    public function actionMsg(){
        $msg = file_get_contents('php://input');
        $obj = DouyinMsg::getTypeObj($msg);
        return $obj->run();

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
