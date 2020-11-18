<?php

namespace app\controllers;

use app\models\WxQr;
use Yii;
use app\components\Func;
use app\models\WxAccount;
use app\models\WxUser;
use app\models\WxMsg;
use yii\web\Controller;

class WeixinController extends Controller
{
    public $wxAccount;
    public $msg_arr;
    public function init(){
        parent::init();
        set_exception_handler([$this,'weixinException']);
        $this->wxAccount = WxAccount::findOne(1);
    }
    //微信扫码登录
    public function actionLogin(){
        $wxQr = new WxQr();
        $wxQr->wx_aid = 1;
        $wxQr->qr_type = WxQr::QR_TYPE_TEMP;
        $wxQr->scene_type = WxQr::SCENE_TYPE_STR;
        $wxQr->scene_value = Func::getcode(32);
        $wxQr->expire = 60;
        $wxQr->ctime = time();
        $wxQr->bindData('app\\models\\wxUser','scanQrLogin',[$wxQr->scene_value]);
        $wxQr->make();

//        $wxQr = WxQr::createTempObj(60,['app\\models\\wxUser','scanQrLogin',[]],1);
        return $this->render('login',['wxQr'=>$wxQr]);

    }
    //轮询检查登录状态
    public function actionCheckLogin(){
        $code = Yii::$app->request->post('code','');
        $open_id = Yii::$app->redis->get('wxlogincode:'.$code);
        $wxUser = WxUser::getObjByOpenid($open_id);
        Yii::$app->user->login($wxUser);
        return 1;
    }
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }
    public function actionMsg()
    {
        //启用微信服务器开发者模式检测
        $this->wxAccount->checkEchostr();
        //验证消息真实性
        if(!$this->wxAccount->checkMsgSign())
        {
            throw new \Exception('sign check error');
        }
        $result = file_get_contents('php://input');
        $this->msg_arr = Func::xmlToArray($result);
        $wxMsg = WxMsg::getTypeObj($this->wxAccount,$this->msg_arr);
        $wxUser = WxUser::getObjByOpenid($wxMsg->openid);
        if(!$wxUser){
            $wxUser = WxUser::addUser($wxMsg->wx_aid, $wxMsg->openid);
        }

        return $wxMsg->reply();

    }
    //自定义异常捕获
    public function weixinException(\Throwable $e)
    {
        Yii::error($e->getMessage().$e->getFile().$e->getLine());
//        //异步客服消息
//        WxAccount::pushCustomMsg([
//            'raw_id'=> $this->msg_arr['ToUserName'],
//            'openid'=> $this->msg_arr['FromUserName'],
//            'msgtype' => 'text',
//            'text' => [
//                'content' => $e->getMessage(),
//            ],
//        ]);

        return 'success';
    }
}
