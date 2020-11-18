<?php

namespace app\models;

use Yii;
use app\components\Func;

/**
 * This is the model class for table "wx_user".
 *
 * @property integer $id
 * @property string $openid
 * @property string $nickname
 * @property integer $sex
 * @property string $province
 * @property string $city
 * @property string $headimgurl
 * @property string $json_data
 */
class WxUser extends \yii\db\ActiveRecord implements \yii\web\IdentityInterface
{
    const BY_TYPE_COMPANY = 1;//公司
    const BY_TYPE_WXUSER = 2;//微信用户

    //获取用户基本信息
    const URL_GET_USERINFO = 'https://api.weixin.qq.com/cgi-bin/user/info?access_token=%s&openid=%s&lang=zh_CN';
    //微信授权页(用户授权后回调ＵＲＬ获取code)ＵＲＬ
    const OAUTH_CODE_URL = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid=%s&redirect_uri=%s&response_type=code&scope=%s&state=%s#wechat_redirect';
    //微信授权获取tokenＵＲＬ
    const OAUTH_TOKEN_URL = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid=%s&secret=%s&code=%s&grant_type=authorization_code';
    //微信授权刷新tokenＵＲＬ
    const OAUTH_REFRESH_TOKEN_URL = 'https://api.weixin.qq.com/sns/oauth2/refresh_token?appid=APPID&grant_type=refresh_token&refresh_token=REFRESH_TOKEN';
    //微信授权刷新tokenＵＲＬ
    const OAUTH_USERINFO_URL = 'https://api.weixin.qq.com/sns/userinfo?access_token=%s&openid=%s&lang=zh_CN';


    public static $sex_options = ['未知','男','女'];
    public static $subscribe_options = ['未关注','已关注'];
    public static $bytype_options = [ 0=>'无', self::BY_TYPE_COMPANY=>'公司', self::BY_TYPE_WXUSER=>'微信用户'];

    //扫码登录
    static public function scanQrLogin($msg,$qr_code){
        Yii::$app->redis->setex('wxlogincode:'.$qr_code,60,$msg->openid);
    }
    /*
     * oauth获取openid（不弹出授权页）,code、state是GET参数关键字，url中不能出现
     */
    static public function oauthOpenid($wxAccount)
    {
        if(isset($_GET['code']) && isset($_GET['state']))
        {
            $token_str = Func::file_get_contents(sprintf(self::OAUTH_TOKEN_URL, $wxAccount->app_id, $wxAccount->app_secret, $_GET['code']));
            $token_arr = json_decode($token_str, 1);
            if(isset($token_arr['errcode']) && $token_arr['errcode'] > 0) throw new \Exception($token_str);
            return $token_arr['openid'];
        }
        $redirect_uri = Yii::$app->request->getAbsoluteUrl();
        $url = sprintf(self::OAUTH_CODE_URL, $wxAccount->app_id, urlencode($redirect_uri), 'snsapi_base', 1);
        header("location:". $url);
        exit;
    }
    /**
     *获取用户对象(弹出授权页，需要授权)
     */
    public function oauthUserObj($scope = 1){
        $info = $this->oauthUserinfo();
        if(empty($info)) throw new \Exception('用户没有授权');
        return WxUser::addUserByWx($info);
    }
    /*
    * 获取用户信息(弹出授权页，需要授权)
    */
    static public function oauthUserinfo($wxAccount)
    {
        //用户同意授权
        if(isset($_GET['code']))
        {
            $user_arr = self::oauthUserByToken(self::oauthTokenByCode($wxAccount, $_GET['code']));
            Yii::$app->session['userinfo'] = $user_arr;
            unset($_GET['code']);
            unset($_GET['state']);
            return $user_arr;
        }
        //用户拒绝授权
        elseif(!isset($_GET['code']) && isset($_GET['state'])){

            unset($_GET['code']);
            unset($_GET['state']);
        }
        //首次进入页面
        else{
            if(empty($redirect_uri)) $redirect_uri = Yii::$app->request->getAbsoluteUrl();
            $url = sprintf(self::OAUTH_CODE_URL, $wxAccount->app_id, urlencode($redirect_uri), 'snsapi_userinfo', 'state');
            header("location:". $url);
            exit;
        }
    }
    static public function oauthTokenByCode($wxAccount, $code)
    {
        $token_str = Func::file_get_contents(sprintf(self::OAUTH_TOKEN_URL, $wxAccount->app_id, $wxAccount->app_secret, $code));
        $token_arr = json_decode($token_str, 1);
        if(isset($token_arr['errcode']) && isset($token_arr['errcode']) > 0)throw new \Exception($token_str);
        return $token_arr;
    }
    static public function oauthUserByToken($token_arr)
    {
        $str = Func::file_get_contents(sprintf(self::OAUTH_USERINFO_URL, $token_arr['access_token'], $token_arr['openid']));
        $arr = json_decode($str,1);
        if(isset($arr['errcode']) && isset($arr['errcode']) > 0)throw new \Exception($str);
        return $arr;
    }

    //根据openid添加用户
    public static function addUserByWx($arr)
    {
        if(!isset($arr['openid'])) return ;
        $obj = self::getObjByOpenid($arr['openid']);
        if(empty($obj)) $obj = new self();
        $obj->openid = $arr['openid'];
        $obj->nickname = $arr['nickname'];
        $obj->sex = $arr['sex'];
        $obj->city = $arr['city'];
        $obj->province = $arr['province'];
        $obj->headimgurl = $arr['headimgurl'];
        $obj->save(false);
        return $obj;
    }
    static public function addUser($wx_aid, $openid){
        $obj = new self();
        $obj->wx_aid = $wx_aid;
        $obj->openid = $openid;
        $obj->nickname = '未知';
        $obj->sex = 0;
        $obj->city = '未知';
        $obj->province = '未知';
        $obj->headimgurl = '';
        $obj->save(false);
        return $obj;
    }
    //根据微信用户基本信息添加用户
    public static function getObjByOpenid($openid)
    {
        return self::find()->where(['openid' => $openid])->one();
    }
    //根据微信用户基本信息添加用户
    public static function getInstance($id)
    {
        return self::findOne($id);
    }
    public function getSubscribeText(){
        return self::$subscribe_options[$this->subscribe];
    }
    //获取头像链接
    public function getHead($size = 64)
    {
        if(!$this->headimgurl) return ;
        return substr($this->headimgurl,0,strrpos($this->headimgurl,'/')).'/'.$size;
    }
    //获取头像链接
    public function getHeadHtml($size = 64)
    {
        $url = $this->getHead($size);
        return $url ? '<img src="'.$url.'" width="'.$size.'"/>' : '';
    }
    //获取性别
    public function getSexText()
    {
        return self::$sex_options[$this->sex];
    }
    //更新用户信息
    public function updateUserInfo(){
        $url = sprintf(self::URL_GET_USERINFO, $this->wxAccount->getToken(), $this->openid);
        $json = Func::file_get_contents($url);
        if(!$json) throw new \Exception('微信获取用户信息接口返回为空', 1);
        $arr = json_decode($json, 1);
        if(!$arr) throw new \Exception('微信获取用户信息接口返回格式错误:'.$json, 1);
        if(isset($arr['errcode']) && $arr['errcode'] > 0) throw new \Exception('微信获取用户信息接口返回错误:'.$json, 1);
        if($arr['subscribe'] == 0) return ;
        $this->subscribe = $arr['subscribe'];
        $this->nickname = $arr['nickname'];
        $this->sex = $arr['sex'];
        $this->city = $arr['city'];
        $this->province = $arr['province'];
        $this->headimgurl = $arr['headimgurl'];
        $this->json_data = $json;
        $this->save(false);
    }
    //关联扩展表
    public function getAttr($type_id = 1){
        return $this->hasMany(WxUserAttr::className(),['wx_uid'=>'id'])->where(['type_id'=>$type_id])->one();
    }
    //关联公众号
    public function getWxAccount(){
        return $this->hasOne(WxAccount::className(),['id'=>'wx_aid']);
    }
    public function beforeSave($insert){
        if(!$this->ctime) $this->ctime = time();
        return parent::beforeSave($insert);
    }
    public function afterSave($insert, $changedAttributes){
        return parent::afterSave($insert, $changedAttributes);
    }
    /**
     * {@inheritdoc}
     */
    public static function findIdentity($id)
    {
        return self::findOne($id);
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthKey()
    {
    }

    /**
     * {@inheritdoc}
     */
    public function validateAuthKey($authKey)
    {
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
    }    /**

/**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'wx_user';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['wx_aid', 'openid', 'nickname', 'sex', 'subscribe', 'province', 'city', 'headimgurl', 'json_data'], 'required'],
            [['wx_aid','sex','subscribe','ctime'], 'integer'],
            [['json_data'], 'string'],
            [['openid'], 'string', 'max' => 28],
            [['nickname', 'province', 'city', 'headimgurl'], 'string', 'max' => 255],
            [['openid'], 'unique']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'wx_aid' => '公众号ID',
            'openid' => 'Openid',
            'subscribe' => '关注',
            'nickname' => '昵称',
            'sex' => '性别',
            'province' => '省份',
            'city' => '城市',
            'headimgurl' => '头像',
            'ctime' => '创建时间',
            'json_data' => 'Json Data',
        ];
    }
}
