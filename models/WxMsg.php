<?php

namespace app\models;

use Yii;
use app\components\Func;

/**
 * This is the model class for table "wx_msg".
 *
 * @property integer $id
 * @property integer $wx_open_app_id
 * @property string $raw_id
 * @property string $openid
 * @property string $msg_type
 * @property string $event
 * @property string $msg_data
 * @property integer $ctime
 * @property string $reply
 */
class WxMsg extends \yii\db\ActiveRecord
{
    //微信全网发布测试帐号
    const WX_PUBLIC_TEST_APPID = 'sss';
    const WX_PUBLIC_TEST_RAWID = 'sss';

    public $msg_arr;
    //回复文本
    public $reply_content;

    protected $wx_msg_reply = [
        'ToUserName' => '',
        'FromUserName' => '',
        'CreateTime' => '',
        'MsgType' => 'text',
        'Content' => '',
    ];

    static public function getTypeObj($wxAccount, $msg_arr){
        $cname = ucfirst(strtolower($msg_arr['MsgType']));
        if(isset($msg_arr['Event'])) $cname .= ucfirst(strtolower(str_replace(['_',' '],'',$msg_arr['Event'])));
        $cname = 'app\\components\\wxmsg\\'.$cname;
        //微信全网发布测试类
        if($msg_arr['ToUserName'] == self::WX_PUBLIC_TEST_RAWID) {
            $cname = 'app\\components\\wxmsg\\PublishMsg';
        }
        if(!class_exists($cname))$cname = 'app\\components\\wxmsg\\DefaultMsg';
        $obj = new $cname();
        $obj->wx_aid = $wxAccount->id;
        $obj->wx_open_app_id = 0;
        $obj->raw_id = $msg_arr['ToUserName'];
        $obj->openid = $msg_arr['FromUserName'];
        $obj->msg_type = $msg_arr['MsgType'];
        if($msg_arr['MsgType'] == 'event'){
            $obj->event = $msg_arr['Event'];
        }
        $obj->msg_data = json_encode($msg_arr,JSON_UNESCAPED_UNICODE);
        $obj->ctime = time();
        $obj->save(false);
        return $obj;
    }
    //获取文本消息内容
    public function getContent(){
        if(empty($this->msg_arr)) $this->msg_arr = Func::xmlToArray($this->msg_data);
        return isset($this->msg_arr['Content']) ? $this->msg_arr['Content'] : '';
    }
    //回复
    public function reply(){
        $this->run();
        if(!$this->reply_content || $this->reply_content == 'success') return 'success';
        $this->wx_msg_reply['ToUserName'] = $this->openid;
        $this->wx_msg_reply['FromUserName'] = $this->raw_id;
        $this->wx_msg_reply['CreateTime'] = time();
        $this->wx_msg_reply['Content'] = $this->reply_content;
        $this->reply = Func::arrayToXml($this->wx_msg_reply);
        $this->save(false);
        if($this->wx_open_app_id){
            return $this->wxOpenApp->encryptMsg($this->reply);
        }else{
            return $this->reply;
        }
    }

    //关联应用
    public function getWxUser(){
        return $this->hasOne(WxUser::className(),['openid'=>'openid']);
    }
    //关联应用
    public function getWxOpenApp(){
        return $this->hasOne(WxOpenApp::className(),['id'=>'wx_open_app_id']);
    }
    //关联应用
    public function getWxAccount(){
        return $this->hasOne(WxAccount::className(),['id'=>'wx_aid']);
    }
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'wx_msg';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['wx_aid', 'wx_open_app_id', 'ctime'], 'integer'],
            [['raw_id', 'openid', 'msg_data', 'ctime'], 'required'],
            [['msg_data', 'reply'], 'string'],
            [['raw_id'], 'string', 'max' => 50],
            [['openid'], 'string', 'max' => 32],
            [['msg_type', 'event'], 'string', 'max' => 255]
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
            'wx_open_app_id' => 'Wx Open App ID',
            'raw_id' => 'Raw ID',
            'openid' => 'Openid',
            'msg_type' => 'Msg Type',
            'event' => 'Event',
            'msg_data' => 'Msg Data',
            'ctime' => 'Ctime',
            'reply' => 'Reply',
        ];
    }
}
