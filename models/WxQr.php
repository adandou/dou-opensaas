<?php

namespace app\models;

use Yii;
use app\components\Func;
use app\models\WxAccount;

/**
 * This is the model class for table "wx_qr".
 *
 * @property integer $id
 * @property integer $wx_aid
 * @property integer $qr_type
 * @property integer $scene_type
 * @property string $scene_value
 * @property integer $expire
 * @property string $ticket
 * @property string $qr_url
 * @property integer $ctime
 */
class WxQr extends \yii\db\ActiveRecord
{
    //获取带叁数二维码ＵＲＬ
    const URL_GET_QR = 'https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=%s';
    const URL_SHOW_QR = 'https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=%s';
    const QR_TYPE_EVER = 1;//永久
    const QR_TYPE_TEMP = 2;//临时
    const SCENE_TYPE_INT = 1;//数字
    const SCENE_TYPE_STR = 2;//字符


    static public $qr_type_options = [
        self::QR_TYPE_EVER => '永久',
        self::QR_TYPE_TEMP => '临时',
    ];
    static public $scene_type_options = [
        self::SCENE_TYPE_INT => '数字',
        self::SCENE_TYPE_STR => '字符',
    ];
    static public $action_arr  = [
        self::QR_TYPE_EVER =>[
            self::SCENE_TYPE_INT => [
                'action_name' => 'QR_LIMIT_SCENE',
                'scene_name' => 'scene_id',
            ],
            self::SCENE_TYPE_STR => [
                'action_name' => 'QR_LIMIT_STR_SCENE',
                'scene_name' => 'scene_str',
            ],
        ],
        self::QR_TYPE_TEMP =>[
            self::SCENE_TYPE_INT => [
                'action_name' => 'QR_SCENE',
                'scene_name' => 'scene_id',
            ],
            self::SCENE_TYPE_STR => [
                'action_name' => 'QR_STR_SCENE',
                'scene_name' => 'scene_str',
            ],
        ],
    ];

    protected $bind_data_arr = [];

    //执行扫描
    static public function runscan($wxMsg){
        $msg_arr = json_decode($wxMsg->msg_data, 1);
        if(!isset($msg_arr['EventKey'])) return ;
        $scene_value = $msg_arr['EventKey'];
        if(substr($msg_arr['EventKey'], 0, 8) == 'qrscene_'){
            $scene_value = substr($msg_arr['EventKey'], 8);
        }
        $wxQr = WxQr::getObjByScene($scene_value);
        if(!$wxQr) throw new \Exception('二维码不存在', 1);
        if(empty($wxQr->bind_data)) return ;
        $wx_qr_bind_data_arr = json_decode($wxQr->bind_data, 1);
        //如果是临时二维码,使用一次即删除
        if($wxQr->qr_type == self::QR_TYPE_TEMP) $wxQr->delete();

        foreach($wx_qr_bind_data_arr as $arr){
            call_user_func_array([$arr[0], $arr[1]], array_merge([$wxMsg], $arr[2]));
        }

    }
    static function getObjByScene($scene_value){
        return self::findOne(['scene_value'=> $scene_value]);
    }
    //创建一个临时二维码
    //$bind_data:['app\\models\\MerchantStaff', 'add', [$merchant_id]]
    static public function createTempObj($expire = 600, $bind_data = [], $wx_aid = 1){
        $wxQr = new WxQr();
        $wxQr->wx_aid = $wx_aid;
        $wxQr->qr_type = WxQr::QR_TYPE_TEMP;
        $wxQr->scene_type = WxQr::SCENE_TYPE_STR;
        $wxQr->scene_value = Func::getcode(32);
        $wxQr->expire = $expire;
        $wxQr->ctime = time();
        $wxQr->bind_data_arr[] = $bind_data;
        $wxQr->make();
        return $wxQr;
    }
    //绑定数据,类名,方法名,参数
    public function bindData($class, $methode, $paras = []){
        $this->bind_data_arr[] = [$class, $methode, $paras];
    }
    public function getQrTypeText(){
        return self::$qr_type_options[$this->qr_type];
    }
    public function getSceneTypeText(){
        return self::$scene_type_options[$this->scene_type];
    }
    //获取微信二维码图片
    public function getPic(){
        return sprintf(self::URL_SHOW_QR, $this->ticket);
    }
    public function make(){
        $arr = [
            'action_name' => self::$action_arr[$this->qr_type][$this->scene_type]['action_name'],
            'action_info' => [
                'scene'=>[self::$action_arr[$this->qr_type][$this->scene_type]['scene_name'] => $this->scene_value],
            ]
        ];
        if($this->qr_type == 2){
            $arr['expire_seconds'] = $this->expire;
        }
        $url = sprintf(self::URL_GET_QR, $this->wxAccount->getToken());
        $str = Func::postData($url, json_encode($arr));
        if(!$str) throw new \Exception('微信接口超时');
        $json = json_decode($str, 1);
        if(!$json) throw new \Exception('微信接口返回错误');
        if(isset($json['errcode'])){
            $this->ticket = $str;
            $this->save(false);
            throw new \Exception('微信接口返回错误:'.$str);
        }
        $this->ticket = $json['ticket'];
        $this->qr_url = $json['url'];
        $this->bind_data = empty($this->bind_data_arr) ? '' : json_encode($this->bind_data_arr, JSON_UNESCAPED_UNICODE);
        $this->save(false);
    }
    //关联公众号
    public function getWxAccount(){
        return $this->hasOne(WxAccount::className(),['id'=>'wx_aid']);
    }
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'wx_qr';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['wx_aid', 'qr_type', 'scene_type', 'scene_value','ctime'], 'required'],
            [['wx_aid', 'qr_type', 'scene_type', 'expire', 'ctime'], 'integer'],
            [['scene_value'], 'string', 'max' => 64],
            [['ticket','qr_url'], 'string', 'max' => 255],
            [['bind_data'], 'string', 'max' => 65536],
            [['scene_value'], 'unique']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'wx_aid' => 'APPID',
            'qr_type' => '类型',
            'scene_type' => '场景值类型',
            'scene_value' => '场景值',
            'expire' => '生存时间(秒)',
            'ticket' => 'Ticket',
            'qr_url' => '二维码',
            'ctime' => '创建时间',
        ];
    }
}
