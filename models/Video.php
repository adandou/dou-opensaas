<?php

namespace app\models;

use Yii;
use OSS;

/**
 * This is the model class for table "video".
 *
 * @property int $id
 * @property int $wx_uid
 * @property int $store 存储类型(1阿里云云/其他待定)
 * @property string $title
 * @property int $cover
 * @property string $path 存储相对路径
 * @property int $size 尺寸
 * @property int $width 宽
 * @property int $height 高
 * @property int $duration 时长(毫秒)
 * @property string $extension 扩展名
 * @property int $ctime
 */
class Video extends \yii\db\ActiveRecord
{
    const KEY = 'xxx';
    const SECRET = 'xxx';
    const ENDPOINT = 'oss-cn-beijing.aliyuncs.com';
    const BUCKET = 'sss';
    static private $ossClient;
    //获取OSSclient
    static private function getOss(){
        if(!self::$ossClient){
            self::$ossClient = new OSS\OssClient(self::KEY,self::SECRET,self::ENDPOINT);
        }
        return self::$ossClient;
    }
    static public function makeObjByUploadInfo($wx_uid,$store,$filename,$title,$size,$duration){
        $obj = new self();
        $obj->wx_uid = $wx_uid;
        $obj->store = $store;
        $obj->title = $title;
        $obj->size = $size;
        $obj->duration = $duration;
        $obj->extension = pathinfo($filename,PATHINFO_EXTENSION);
        $obj->ctime=time();
        if(!$obj->save()){
            throw new \Exception(json_encode($obj->getErrors()));
        }
        return $obj;
    }
    //获取部分数据
    public function getPartData($start=0, $offset = 5000000){
        $end = $start + $offset -1;
        $yu = $this->size - $start;
        //如果剩余数据长度小于分块大小则结束标记设空，否则OSS sdk报错
        if($yu <= $offset){
            $end = '';
        }
        $options = array(OSS\OssClient::OSS_RANGE => implode('-',[$start,$end]));
        $content = self::getOss()->getObject(self::BUCKET, $this->getStoreFile(1), $options);
        return $content;
    }
    public function showSize(){
        return round($this->size/1024/1024,2)."M";
    }
    public function showDuration(){
        return floor($this->duration/1000/60).'分'.($this->duration/1000%60).'秒';
    }
    public function getStorePath(){
        return date('Ymd',$this->ctime);
    }
    public function getStoreFile($path=false){
        $file = $this->id.'.'.$this->extension;
        if($path){
            $file = $this->getStorePath().'/'.$file;
        }
        return $file;
    }
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'video';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['wx_uid', 'store', 'cover', 'size', 'width', 'height', 'duration', 'ctime'], 'integer'],
            [['title', 'path'], 'string', 'max' => 255],
            [['extension'], 'string', 'max' => 4],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'wx_uid' => 'Wx Uid',
            'store' => 'Store',
            'title' => '标题',
            'cover' => '封面',
            'path' => 'Path',
            'size' => '尺寸',
            'width' => '宽',
            'height' => '高',
            'duration' => '时长',
            'extension' => 'Extension',
            'ctime' => '创建时间',
        ];
    }
}
