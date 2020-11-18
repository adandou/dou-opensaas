<?php

namespace app\models;

use Yii;
use OSS;
use yii\filters\Cors;

/**
 * This is the model class for table "upload_file".
 *
 * @property int $id
 * @property int $type
 * @property string $suffix 后缀名
 * @property string $path
 * @property int $ctime
 */
class UploadFile extends \yii\db\ActiveRecord
{
    const KEY = 'xxx';
    const SECRET = 'xxx';
    const ENDPOINT = 'oss-cn-beijing.aliyuncs.com';
    const PUBLIC_BUCKET = 'public-oss-pic';

    const TYPE_SYSTEM = 1;//系统
    const TYPE_LOCAL = 2;//本地
    const TYPE_OSS = 3;//OSS
    static private $ossClient;
    //获取OSSclient
    static private function getOss(){
        if(!self::$ossClient){
            self::$ossClient = new OSS\OssClient(self::KEY,self::SECRET,self::ENDPOINT);
        }
        return self::$ossClient;
    }
    //上传到OSS，来源于普通图片
    static public function addToOssByFile($file, $suffix = null){
//        $file = iconv('utf-8','gbk',$file);
        try{
            $file_content = file_get_contents($file,
                false, stream_context_create([
                    "ssl" => [
                        "verify_peer"=>false,
                        "verify_peer_name"=>false,
                    ]
                ]));
        }catch (\Exception $e){
            echo $e->getMessage()."\n\r";
            return;
        }
        if(!$suffix) $suffix = substr($file,(strrpos($file,'.')+1));
        $obj = new self();
        $obj->type= self::TYPE_OSS;
        $obj->suffix=$suffix;
        $obj->path='pic/'.date('Ymd');
        $obj->ctime=time();
        if($obj->save(false)){
            self::getOss()->putObject(self::PUBLIC_BUCKET,$obj->path.'/'.$obj->getFilename(),$file_content);
        }else{
            throw new \Exception(json_encode($obj->getErrors(),JSON_UNESCAPED_UNICODE), 1);
        }
        return $obj;
    }
    //上传到OSS，来源于base64图片
    static public function addToOssByBase64($base64_str=''){
        $base64_arr= explode(',', $base64_str);
//        echo $base64_arr[0];exit;
        preg_match('/\/([^\;]+)\;/',$base64_arr[0],$matchs);
        $suffix = $matchs[1];
        $obj = new self();
        $obj->type= self::TYPE_OSS;
        $obj->suffix=$suffix;
        $obj->path='pic/'.date('Ymd');
        $obj->ctime=time();
        if($obj->save(false)){
            self::getOss()->putObject(self::PUBLIC_BUCKET,$obj->path.'/'.$obj->getFilename(),base64_decode($base64_arr[1]));
        }else{
            throw new \Exception(json_encode($obj->getErrors(),JSON_UNESCAPED_UNICODE), 1);
        }
        return $obj;
    }
    //上传到OSS，来源于上传文件
    static public function addToOssByUpload($file = 'file'){
        $tmp = $_FILES[$file]['tmp_name'];
//        throw new \Exception('tmp='.$tmp,1);
        $mime = OSS\Core\MimeTypes::getMimetype($_FILES[$file]['name']);
//        print_r($mime);exit;
        $suffix = substr($_FILES[$file]['name'],(strrpos($_FILES[$file]['name'],'.')+1));
        $obj = new self();
        $obj->type= self::TYPE_OSS;
        $obj->suffix=$suffix;
        $obj->path='pic/'.date('Ymd');
        $obj->ctime=time();
        if($obj->save(false)){
            $oss = self::getOss()->putObject(self::PUBLIC_BUCKET,$obj->path.'/'.$obj->getFilename(),file_get_contents($tmp));
        }else{
            throw new \Exception(json_encode($obj->getErrors(),JSON_UNESCAPED_UNICODE), 1);
        }
//        return print_r($oss);
        return $obj;
    }
    //上传图片，来源于旧数据
    static public function addByOld($file,$ctime='',$type = 11){
        $suffix = substr($file,(strrpos($file,'.')+1));
        $obj = new self();
        $obj->type= $type;
        $obj->suffix=$suffix;
        $obj->path=$file;
        $obj->ctime= $ctime ?: time();
        if(!$obj->save(false)){
            throw new \Exception(json_encode($obj->getErrors(),JSON_UNESCAPED_UNICODE), 1);
        }
        return $obj;
    }
    //获取一个对象
    static public function getInstance($id)
    {
        return self::findOne($id);
    }
    private function getPathFile(){
        return 'pic/'.date('Ymd/').$this->ctime.$this->id;
    }
    public static function getBaseDir()
    {
        return dirname(__DIR__)."/runtime/upload";
    }
    public static function getBaseUrl()
    {
        return Yii::$app->urlManager->createUrl(['/']);
    }
    //获取路径
    static public function getPath($id, $ctime){
        return date('Ymd/H', $ctime);
    }
    //获取图片存储路径
    public function getFilePath()
    {
        return self::getBaseDir().'/'.self::getPath($this->id,$this->ctime).'/'.$this->getFilename($size);
    }
    //获取图片URL
    public function getFileUrl()
    {
        $url = $this->getFileRawUrl();
//        $url.="?x-oss-process=image/watermark,image_d2F0ZXIucG5nP3gtb3NzLXByb2Nlc3M9aW1hZ2UvcmVzaXplLFBfMzU=,g_center";
        return $url;
    }
    //获取图片原始URL
    public function getFileRawUrl()
    {
        switch ($this->type){
            case self::TYPE_OSS:
                $url = 'http://pic.ttmei.vip/'.$this->path.'/'.$this->getFilename();
                break;
            case self::TYPE_SYSTEM:
                $url = 'https://football.ttmei.vip';
                if(!empty($this->path)){
                    $url .= '/'.$this->path;
                }
                $url .= '/'.$this->id.'.'.$this->suffix;
                break;
            default:
                $url = Yii::$app->params['old_pic_host'].$this->path;
        }
        return $url;
    }
    //获取文件名
    public function getFilename()
    {
        return $this->ctime.$this->id.'.'.$this->suffix;
    }
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'upload_file';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['type', 'ctime'], 'required'],
            [['type', 'ctime'], 'integer'],
            [['suffix', 'path'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'type' => 'Type',
            'suffix' => 'Suffix',
            'path' => 'Path',
            'ctime' => 'Ctime',
        ];
    }
}
