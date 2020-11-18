<?php

namespace app\models;

use Yii;
use yii\helpers\Html;

/**
 * This is the model class for table "auth_item".
 *
 * @property int $id
 * @property int $auth_app_id 授权应用ID
 * @property int $auth_account_id 授权账号ID
 * @property int $video_id 视频ID,等于0表示官方平台同步，大于零为本平台视频ID
 * @property string|null $item_id 官方平台ID
 * @property string|null $title 标题
 * @property string|null $cover 封面
 * @property string|null $show_url 查看链接
 * @property int $is_top 是否置顶
 * @property int $comment_count 评论数
 * @property int $digg_count 点赞数
 * @property int $download_count 下载数
 * @property int $play_count 播放数
 * @property int $share_count 分享数
 * @property int $forward_count 转发数
 * @property int $timing 1定时发布2立即发布
 * @property int $post_time 发布时间
 * @property int $create_time 创建时间
 * @property int $state 状态1已发布2未发布3发布中
 */
class AuthItem extends \yii\db\ActiveRecord
{
    static public $state_arr = [
        1 => '已发布',
        2 => '未发布',
        3 => '发布中',
    ];
    static public $timing_arr = [
        1 => '定时发布',
        2 => '立即发布',
    ];
    //评论回复
    public function commentReply($authComment,$content){
        return $this->authAccount->commentReply($this,$authComment,$content);

    }
    //批量发布即将到期的项目
    static public function publishItems($interval = 60){
        $start = time() -10;
        $end = time() + 10 + $interval;
//        $objs = self::find()->andWhere("post_time>".$start)->andWhere("post_time<".$end)->andWhere(['state'=>2])->all();
        $objs = self::find()->andWhere("post_time<".$end)->andWhere(['state'=>2])->all();
        foreach($objs as $obj){
            $obj->publishItem();
        }
    }
    //发布单个项目
    public function publishItem(){
        $this->state=3;
        $this->save();
        $item_id = $this->authAccount->publishItem($this);
        $this->item_id=$item_id;
        $this->post_time=time();
        $this->state=1;
        $this->save();
    }
    //同步更新单个发布的数据
    public function syncComments(){
        $this->authAccount->syncComments($this);
    }
    //同步更新单个发布的数据
    public function syncItem(){
        $this->authAccount->syncItem($this);
    }
    //显示封面
    public function showCover(){
        return Html::img(str_replace(['https:','http:'],['',''],$this->cover),['width'=>60]);
    }
    public function showState(){
        return self::$state_arr[$this->state]??'未知';
    }
    public function showTiming(){
        return self::$timing_arr[$this->timing]??'未知';
    }
    //关联抖音账号
    public function getAuthAccount(){
        return $this->hasOne(AuthAccount::className(),['id'=>'auth_account_id']);
    }
    //关联视频
    public function getVideo(){
        return $this->hasOne(Video::className(),['id'=>'video_id']);
    }
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'auth_item';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['auth_app_id', 'auth_account_id', 'video_id', 'is_top', 'comment_count', 'digg_count', 'download_count', 'play_count', 'share_count', 'forward_count', 'timing', 'post_time', 'create_time', 'state'], 'integer'],
            [['auth_account_id'], 'required'],
            [['item_id'], 'string', 'max' => 100],
            [['title'], 'string', 'max' => 512],
            [['cover'], 'string', 'max' => 255],
            [['show_url'], 'string', 'max' => 500],
            [['item_id'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'auth_app_id' => '应用',
            'auth_account_id' => '账号',
            'video_id' => '视频ID',
            'item_id' => 'Item ID',
            'title' => '标题',
            'cover' => '封面',
            'show_url' => '查看链接',
            'is_top' => 'Is Top',
            'comment_count' => '评论数',
            'digg_count' => '点赞数',
            'download_count' => '下载数',
            'play_count' => '播放数',
            'share_count' => '分享数',
            'forward_count' => '转发数',
            'timing' => '定时发布',
            'post_time' => '发布时间',
            'create_time' => '创建时间',
            'state' => '状态',
        ];
    }
}
