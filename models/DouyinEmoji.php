<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "douyin_emoji".
 *
 * @property int $id
 * @property string $name
 * @property string $img
 * @property int $hide
 */
//抖音评论表情对照数据
//https://sf1-hscdn-tos.pstatp.com/obj/ies-fe-bee/bee_prod/biz_181/bee_prod_181_bee_publish_1343.json
//抖音表情图片地址
//https://sf3-ttcdn-tos.pstatp.com/obj/ies-douyin-opencn/emoji/weixiao-3x.png

class DouyinEmoji extends \yii\db\ActiveRecord
{
    const EMOJI_IMG_PATH = '//sf3-ttcdn-tos.pstatp.com/obj/ies-douyin-opencn/emoji/';
    static $emoji_arr = [];
    static $emoji_img_name_arr = [];
    //替换表情为图片显示
    static public function showContent($content){
        self::initEmojiArr();
        $content = preg_replace_callback('/\[([^\]]+)\]/',function($matchs){
//            print_r($matchs);
            return '<img src="'.self::$emoji_arr[$matchs[1]].'" width="20"/>';
        },$content);
        return $content;
    }
    //替换图片为表情
    static public function inputContent($content){
        self::initEmojiArr();
        $content = preg_replace_callback('/\<img[^\=]+\=\"([^\"]+)\"[^\>]*>/',function($matchs){
//            print_r($matchs);
            $info = pathinfo($matchs[1]);
            $arr = explode('-',$info['filename']);
            $img = $arr[0];
            return '['.self::$emoji_img_name_arr[$img].']';
//            return '<img src="'.self::$emoji_arr[$matchs[1]].'" width="20"/>';
        },$content);
        return $content;
    }
    //获取表情数组
    static public function initEmojiArr(){
        if(!empty(self::$emoji_arr)) return ;
        $objs = self::find()->all();
        foreach($objs as $obj){
            self::$emoji_arr[$obj->name] = $obj->getImg();
            self::$emoji_img_name_arr[$obj->img] = $obj->name;
        }
    }
    //获取表情html
    static public function getEmojiHtml(){
        $objs = self::find()->where(['hide'=>0])->all();
        $html = '';
        foreach($objs as $obj){
            $html.='<img src="'.$obj->getImg().'" onclick="chooseEmoji(this)" />';
        }
        return $html;
    }

    //获取表情图片地址
    public function getImg(){
        return self::EMOJI_IMG_PATH.$this->img.'-3x.png';
    }
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'douyin_emoji';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'img', 'hide'], 'required'],
            [['hide'], 'integer'],
            [['name', 'img'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'img' => 'Img',
            'hide' => 'Hide',
        ];
    }
}
