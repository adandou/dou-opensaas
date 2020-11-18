<?php

namespace app\modules\weixin;

use Yii;
use yii\web\Controller;
use yii\helpers\Url;

use app\models\WxOpenApp;
use app\models\WxAccount;
use app\models\WxUser;
use app\models\UserTag;
use app\models\WxUserTag;
use app\models\Merchant;
use app\models\MerchantStaff;



class WeixinController extends Controller
{
    public $layout = 'weixin';
    public $open_appid;//微信开放平台应用appid
    public $appid;//商家微信公众号appid
    public $wxOpenApp;//微信开放平台应用对象
    public $wxAccountObj;//商家微信公众号对象
    public $oauth_scope = 1;//网页授权范围:0不用授权/1普通静默授权/2需要用户同意并能获取用户信息的授权
    public $open_id;//微信用户唯一标识
    public $wxUserObj;//微信用户对象
    protected $merchant_id;//商户ID

    public function init(){
        return ;
        $this->open_appid = Yii::$app->request->get('open_appid',0);
        $this->appid = Yii::$app->request->get('appid','wxf5b48f318b765354');
        if($this->open_appid){
            $this->wxOpenApp = WxOpenApp::getObjByAppId($this->open_appid);
            if(!$this->wxOpenApp)throw new \Exception('微信应用不存在',1);
        }
        if($this->appid){
            $this->wxAccountObj = WxAccount::getObjByAppId($this->appid);
            if(!$this->wxAccountObj)throw new \Exception('微信公众号不存在',1);
        }
        //debug
        //$this->wxUserObj=WxUser::getInstance(1);

        switch($this->oauth_scope){
            case 1:
            {
                if(!$this->appid) throw new \Exception('微信公众号不存在');
                $this->open_id = $this->getOpenId();
                if(!$this->open_id) throw new \Exception('openid获取失败', 1);
                $this->wxUserObj = WxUser::getObjByOpenid($this->open_id);
                if(!$this->wxUserObj){
                    $this->wxUserObj = WxUser::addUser($this->wxAccountObj->id, $this->open_id);
                }
                break;
            }
            case 2:
            {
                if(!$this->appid) throw new \Exception('微信公众号不存在');
                $info = $this->getUserInfo();
                $this->wxUserObj = WxUser::addUserByWx($info);
                break;
            }
            default:{

                break;
            }
        }
    }
    public function getOpenId(){
        if(Yii::$app->session->get('open_id')) return Yii::$app->session->get('open_id');
        //测试专用
        if(isset($_GET['debug']))return 'oTjYP1ke0B-2YeL1GqiDlFdM9';//游山玩水
        $tourl = Yii::$app->request->getAbsoluteUrl();
        $redirect_uri = Yii::$app->urlManager->createAbsoluteUrl(['/weixin/open/oauthopenid','open_appid'=>$this->open_appid,'appid'=>$this->appid,'tourl'=>$tourl]);
        header('location:'.$redirect_uri);
        exit;
    }
    public function getUserInfo(){
        if(Yii::$app->session->get('userinfo')) return Yii::$app->session->get('userinfo');

        //测试专用
        if(isset($_GET['debug'])){
            $userinfo = '{"openid":"oWKd7jlsKH3gBkKHOKs5bmQ0dmEk",
            "nickname":"darwinxu",
            "sex":1,
            "city":"广州",
            "province":"广东",
            "country":"中国",
            "headimgurl":"http://wx.qlogo.cn/mmopen/7SPO0mRJt6DEtusyFI0Ou119xOMU4v7gicV5iayGUaQCSbOBkiarrBGtyWO4IhSH6ic34ZzqMoViciayWJDcFmuHWcNg/0"}';
            return $userinfo;
        }
        $userinfo = WxUser::oauthUserinfo($this->wxAccountObj);
        Yii::$app->session->set('userinfo', $userinfo);
        return $userinfo;
    }
    //选择商户,
    //如果当前角色只对一家商户有权限则自动在当前url加上有权限的商户ID并刷新页面
    //如果当前角色对多家商户有权限则跳转到商户选择页
    protected function checkMearchant($tag_id){
        $this->merchant_id = Yii::$app->request->get('merchant_id', 0);
        if(!$this->merchant_id){
            $merchant_ids = WxUserTag::find()->select(['merchant_id'])->where([
                'wx_uid' => $this->wxUserObj->id,
                'tag_id'=>$tag_id,
            ])->column();
            if(empty($merchant_ids)) throw new \Exception('无权操作', 1);
            if(count($merchant_ids)>1){
                echo $this->render('@app/modules/weixin/views/layouts/choose-merchant', ['objs' => Merchant::find()->where(['id'=>$merchant_ids])->all()]);
                exit;
            }
            $this->merchant_id = current($merchant_ids);
            header("Location:".Url::current(['merchant_id'=>$this->merchant_id]));
            exit;
        }
        $auth = WxUserTag::find()->where([
            'wx_uid' => $this->wxUserObj->id,
            'merchant_id'=>$this->merchant_id,
            'tag_id'=>$tag_id,
        ])->count();
        if(!$auth) throw new \Exception('无权操作', 1);
    }
    //是否是管理员
    protected function isAdmin(){
        return WxUserTag::find()->where(['wx_uid' => $this->wxUserObj->id,'tag_id'=>UserTag::ADMIN])->count();
    }
    //是否是商户管理员
    protected function isMerchantAdmin($merchant_id){
        //return 1;
        return WxUserTag::find()->where([
            'wx_uid' => $this->wxUserObj->id,
            'merchant_id'=>$merchant_id,
            'tag_id'=>UserTag::MERCHANT_ADMIN,
        ])->count();
    }
    //是否是商户收银员
    protected function isMerchantCashier($merchant_id){
        //return 1;
        return WxUserTag::find()->where([
            'wx_uid' => $this->wxUserObj->id,
            'merchant_id'=>$merchant_id,
            'tag_id'=>UserTag::MERCHANT_CASHIER,
        ])->count();
    }
    //是否是商户员工
    protected function isStaff($merchant_id, $wx_uid = 0){
        $wx_uid = $wx_uid?:$this->wxUserObj->id;
        return MerchantStaff::find()->where(['wx_uid' => $wx_uid,'merchant_id'=>$merchant_id])->count();
    }
    //统一报错
    protected function error(\Exception $e){
        $arr = [
            'code' => $e->getCode(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'msg' => $e->getMessage(),
        ];
        //print_r($arr);
        Yii::error($arr,'merchant');

        return $this->render('@app/modules/weixin/views/layouts/error', ['e' => $e,]);
    }
    //统一成功
    protected function success($success = ''){
        return $this->render('@app/modules/weixin/views/layouts/success', ['success' => $success,]);
    }
    //统一报错AJAX
    protected function errorAjax(\Exception $e){
        return json_encode(['errcode'=>$e->getCode(),'errmsg'=>$e->getMessage()],JSON_UNESCAPED_UNICODE);
    }
    //统一报错AJAX
    protected function successAjax($arr = []){
        return json_encode(array_merge(['errcode'=>0,'errmsg'=>''],$arr),JSON_UNESCAPED_UNICODE);
    }
}
