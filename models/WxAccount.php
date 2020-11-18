<?php

namespace app\models;

use Yii;
use yii\console\Request;
use app\components\Func;

/**
 * This is the model class for table "wx_account".
 *
 * @property integer $id
 * @property string $name
 * @property string $wx_no
 * @property string $raw_id
 * @property string $app_id
 * @property string $app_secret
 * @property string $msg_token
 */
class WxAccount extends \yii\db\ActiveRecord
{
    //获取tokenＵＲＬ
    const URL_GET_TOKEN = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=%s&secret=%s';
    //获取带叁数二维码ＵＲＬ
    const URL_GET_QR = 'https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=%s';
    //生成自定义菜单
    const URL_CREATE_MENU = 'https://api.weixin.qq.com/cgi-bin/menu/create?access_token=%s';
    //获取用户基本信息
    const URL_GET_USERINFO = 'https://api.weixin.qq.com/cgi-bin/user/info?access_token=%s&openid=%s&lang=zh_CN';
    // 获取 JS-SDK Ticket：http://mp.weixin.qq.com/wiki/7/aaa137b55fb2e0456bf8dd9148dd613f.html
    const URL_GET_TICKET = 'https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token=%s&type=%s';
    //下载多媒体文件url
    const URL_GET_MEDIA = 'http://file.api.weixin.qq.com/cgi-bin/media/get?access_token=%s&media_id=%s';
    //授权后的过oauth url
    const URL_OAUTH_CODE = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid=%s&redirect_uri=%s&response_type=code&scope=%s&state=%s#wechat_redirect';
    const URL_OAUTH_TOKEN = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid=%s&secret=%s&code=%s&grant_type=authorization_code';
    //上传图片
    const URL_UPLOAD_IMG = 'https://api.weixin.qq.com/cgi-bin/media/uploadimg?access_token=%s';

    const URL_OAUTH_CODE_COMPONENT = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid=%s&redirect_uri=%s&response_type=code&scope=%s&state=%s&component_appid=%s#wechat_redirect';
    const URL_OAUTH_TOKEN_COMPONENT = 'https://api.weixin.qq.com/sns/oauth2/component/access_token?appid=%s&code=%s&grant_type=authorization_code&component_appid=%s&component_access_token=%s';
    //发送客服消息
    const URL_SEND_MSG = 'https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token=%s';
    //发送模板消息
    const URL_TEMPLATE_MSG = 'https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=%s';
    //门店创建
    const URL_SHOP_ADD = 'https://api.weixin.qq.com/cgi-bin/poi/addpoi?access_token=%s';

    const SYSTEM_ID = 1;//系统自身微信公众号id

    const QUEUE_WX_CUSTOM_MSG = 'wx_custom_msg';//微信客服消息队列名
    const QUEUE_WX_TPL_MSG = 'wx_tpl_msg';//微信模板消息队列名
    static protected $systemWxAccount;//系统自己的微信公众号对象

    public $oauth_token;
    public $openid;

    //获取系统自身微信公众号
    static public function getSystem(){
        if(self::$systemWxAccount) return self::$systemWxAccount;
        self::$systemWxAccount = self::getInstance(self::SYSTEM_ID);
        return self::$systemWxAccount;
    }
    //统一检查微信接口返回数据是否错误
    static public function checkError($wx_result){
        $arr = json_decode($wx_result, 1);
        if(isset($arr['errcode']) && $arr['errcode'] > 0){
            throw new \Exception('微信接口:'.$arr['errmsg'], $arr['errcode']);
        }
        return $arr;
    }
    //微信客服消息入队列
    /*
    [
        'wx_aid' =>wx_aid\appid\raw_id 至少一个有值
        'appid'=>wx_aid\appid\raw_id 至少一个有值
        'raw_id'=>wx_aid\appid\raw_id 至少一个有值

        'openid'=>openid\wx_uid 至少一个有值
        'wx_uid'=>openid\wx_uid 至少一个有值

        其他参考微信客服消息接口文档
    ]
    */
    static public function pushCustomMsg(array $arr){
        if(!(isset($arr['wx_aid'])||isset($arr['appid'])||isset($arr['raw_id']))) throw new \Exception('pushCustomMsg para error', 1);
        if(!(isset($arr['wx_uid'])||isset($arr['openid']))) throw new \Exception('pushCustomMsg para error', 1);
        Yii::$app->redis_queue->rpush(self::QUEUE_WX_CUSTOM_MSG,json_encode($arr,JSON_UNESCAPED_UNICODE));
    }
    //微信模板消息入队列
    /*
    [
        'wx_aid' =>wx_aid\appid\raw_id 至少一个有值
        'appid'=>wx_aid\appid\raw_id 至少一个有值
        'raw_id'=>wx_aid\appid\raw_id 至少一个有值

        'openid'=>openid\wx_uid 至少一个有值
        'wx_uid'=>openid\wx_uid 至少一个有值

        其他参考微信接口文档
    ]
    */
    static public function pushTplMsg(array $arr){
        if(!(isset($arr['wx_aid'])||isset($arr['appid'])||isset($arr['raw_id']))) throw new \Exception('pushTplMsg para error', 1);
        if(!(isset($arr['wx_uid'])||isset($arr['openid']))) throw new \Exception('pushTplMsg para error', 1);
        Yii::$app->redis_queue->rpush(self::QUEUE_WX_TPL_MSG,json_encode($arr,JSON_UNESCAPED_UNICODE));
    }
    //发送客服消息
    public function sendMsg($content){
        $url = sprintf(self::URL_SEND_MSG, $this->getToken());
        Func::postData($url, $content);
    }
    //发送客服消息
    public function sendCustomMsg(array $arr){
        $url = sprintf(self::URL_SEND_MSG, $this->getToken());
        Func::postData($url, json_encode($arr,JSON_UNESCAPED_UNICODE));
    }
    //发送模板消息
    public function sendTemplateMsg(array $template_data_arr){
        $url = sprintf(self::URL_TEMPLATE_MSG, $this->getToken());
        return self::checkError(Func::postData($url, json_encode($template_data_arr,JSON_UNESCAPED_UNICODE)));
    }
    /*
     * 下载多媒体文件
     */
    public function downloadMediaFile($media_id, $file)
    {
        $url = sprintf(self::URL_GET_MEDIA, $this->getToken(), $media_id);
        file_put_contents($file, file_get_contents($url));

    }

    //生成自定义菜单
    public function createMenu($menu_json)
    {
        $url = sprintf(self::URL_CREATE_MENU, $this->getToken());
        Func::postData($url, $menu_json);
    }
    //获取jsapi页面配置文件
    public function makeJsapiConfig($api_list)
    {
        $noncestr = Func::getcode(32);
        $timestamp = time();
        $arr = [
            'noncestr' => $noncestr,
            'timestamp' => $timestamp,
            'jsapi_ticket' => $this->getTicket(),
            'url' => Yii::$app->request->getAbsoluteUrl(),
        ];
        ksort($arr, SORT_STRING);
        $str = Func::http_build_str($arr);
        $sign = sha1($str);
        return array(
            // 'debug' => YII_DEBUG,   // JS-SDK debug
            'debug' => false,
            'appId' => $this->app_id,
            'timestamp' => $timestamp,
            'nonceStr' => $noncestr,
            'signature' => $sign,
            'jsApiList' => $api_list,
        );
    }
    //获取ticket   type=jaspi/wx_card
    public function getTicket($flush = 0, $type = 'jsapi'){
        $key = 'wx:'.$type.':ticket:'.$this->id;
        //强制刷新ticket
        if($flush)
        {
            $url = sprintf(self::URL_GET_TICKET, $this->getToken(),$type);
            $t = json_decode(Func::file_get_contents($url), 1);
            if(!isset($t['ticket']))
            {
                throw new \Exception($this->app_id.':'.json_encode($t));
            }
            yii::$app->cache->set($key, $t['ticket'], ($t['expires_in'] - 60));
            return $t['ticket'];
        }
        $value = yii::$app->cache->get($key);
        if(!empty($value)) return $value;
        return call_user_func_array(array($this, __METHOD__), array(1,$type));
    }

    //过oauth获取openid
    public function getUrlByOauthCode($redirect_uri = null, $scope = 'snsapi_base'){
        if(!$redirect_uri)$redirect_uri = Yii::$app->request->getAbsoluteUrl();
        $url = sprintf(self::URL_OAUTH_CODE, $this->app_id, urlencode($redirect_uri), $scope, session_id());
        return $url;
    }
    //过oauth获取openid
    public function getUrlByOauthToken($code){
        $url = sprintf(self::URL_OAUTH_TOKEN, $this->app_id, $this->app_secret, $code);
        return $url;
    }
    //获取token
    public function getToken($flush = 0){
        //强制刷新token
        if($flush)
        {
            $url = sprintf(self::URL_GET_TOKEN, $this->app_id, $this->app_secret);
            $arr = self::checkError(Func::file_get_contents($url));
            $this->access_token = $arr['access_token'];
            $this->access_token_expire = time() + $arr['expires_in'];
            $this->save();
            $token = $this->access_token;

        }elseif($this->access_token_expire <= time()){
            $token = call_user_func_array(array($this, __METHOD__), array(1));
        }else{
            $token = $this->access_token;
        }
        return $token;
    }
    //根据授权信息初始化并保存
    public function initByAuthorizerInfo($wx_open_app_account_id){
        $arr = json_decode($this->authorizer_info, 1);
        $info = $arr['authorizer_info'];
        $this->name=$info['nick_name'];
        $this->raw_id=$info['user_name'];
        $this->app_id=$arr['authorization_info']['authorizer_appid'];
        $this->wx_open_app_account_id=$wx_open_app_account_id;
        $this->save(false);
    }
    /**
     * 上传微信消息素材
     * file: 文件绝对路径
     * type: 图片(image）、语音（voice）、视频（video）和缩略图（thumb)
     */
    public function uploadimg($file)
    {
        $url = sprintf(self::URL_UPLOAD_IMG, $this->getToken());
        $cmd = 'curl -F buffer=@'.$file.' "'.$url.'"';
        exec($cmd, $output);
        return array_pop($output);
    }
    //创建门店
    public function addShop($json_arr){

        $url = sprintf(self::URL_SHOP_ADD, $this->getToken());

        $arr = WxAccount::checkError(Func::postData($url, json_encode($json_arr,JSON_UNESCAPED_UNICODE)));
        return $arr;
    }
    //获取支付组件
    public function getWxpay(){
        return new \app\components\Wxpay($this);
    }
    public static function getInstance($id = 1)
    {
        return self::findOne($id);
    }
    static public function getObjByRawId($raw_id){
        return self::findOne(['raw_id'=>$raw_id]);
    }
    static public function getObjByAppId($app_id){
        return self::findOne(['app_id'=>$app_id]);
    }
    //获取下拉菜单选项
    static public function getOptions(){
        $objs = self::find()->where(1)->all();
        $ops = [];
        foreach($objs as $obj){
            $ops[$obj->id] = $obj->name;
        }
        return $ops;
    }
    //检查微信通知或事件的密钥
    public function checkMsgSign(){
        $t = isset($_GET['timestamp']) ? $_GET['timestamp'] : '';
        $n = isset($_GET['nonce']) ? $_GET['nonce'] : '';
        $signature = isset($_GET['signature']) ? $_GET['signature'] : '';

        $sign_arr = [$this->msg_token,$t,$n];
        sort($sign_arr, SORT_STRING);
        return sha1( implode( $sign_arr ) ) == $signature;
    }
    //检查启用微信服务器开发模式(非授权)
    public function checkEchostr(){
        if(!isset($_GET["echostr"])) return ;
        $signature = isset($_GET["signature"]) ? $_GET["signature"] : '';
        $timestamp = isset($_GET["timestamp"]) ? $_GET["timestamp"] : '';
        $nonce = isset($_GET["nonce"]) ? $_GET["nonce"] : '';
        $tmpArr = array($this->msg_token, $timestamp, $nonce);
        sort($tmpArr, SORT_STRING);
        $tmpStr = implode( $tmpArr );
        $tmpStr = sha1( $tmpStr );
        if( $tmpStr == $signature ){
            echo $_GET["echostr"];
        }
        exit;
    }
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'wx_account';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'raw_id', 'app_id'], 'required'],
            [['access_token_expire','wx_open_app_account_id'], 'integer'],
            [['authorizer_info'], 'string'],
            [['name', 'wx_no', 'raw_id', 'app_id', 'app_secret', 'msg_token', 'access_token'], 'string', 'max' => 255],
            [['raw_id'], 'unique'],
            [['app_id'], 'unique'],
            [['wx_no'], 'unique']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'wx_no' => 'Wx No',
            'raw_id' => 'Raw ID',
            'app_id' => 'App ID',
            'app_secret' => 'App Secret',
            'msg_token' => 'Msg Token',
            'access_token' => 'Access Token',
            'access_token_expire' => 'Access Token Expire',
            'authorizer_info' => 'Authorizer Info',
            'wx_open_app_account_id' => '授权信息',
        ];
    }
}
