<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "auth_comment".
 *
 * @property int $id
 * @property int $auth_app_id
 * @property int $auth_account_id
 * @property int $auth_item_id
 * @property int $auth_uid
 * @property int $parent_id
 * @property string $comment_id
 * @property string $content
 * @property int $is_top
 * @property int $digg_count
 * @property int $reply_count
 * @property int $create_time
 * @property int $post_time
 */
class AuthComment extends \yii\db\ActiveRecord
{
    //获取对象
    static public function getObjByCommentId($comment_id){
        return self::find()->where(['comment_id'=>$comment_id])->one();
    }
    //评论回复
    public function commentReply($content){
        return $this->authItem->commentReply($this,$content);
    }
    //显示评论
    public function showContent(){
        return DouyinEmoji::showContent($this->content);
    }
    //关联父评论
    public function getParentComment(){
        return $this->hasOne(AuthComment::className(),['id'=>'parent_id']);
    }
    //关联
    public function getAuthItem(){
        return $this->hasOne(AuthItem::className(),['id'=>'auth_item_id']);
    }
    //关联
    public function getAuthAccount(){
        return $this->hasOne(AuthAccount::className(),['id'=>'auth_account_id']);
    }
    //关联
    public function getAuthUser(){
        return $this->hasOne(AuthUser::className(),['id'=>'auth_uid']);
    }
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'auth_comment';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['auth_app_id', 'auth_account_id', 'auth_item_id', 'auth_uid', 'parent_id', 'is_top', 'digg_count', 'reply_count', 'create_time', 'post_time'], 'integer'],
            [['auth_item_id', 'comment_id', 'content'], 'required'],
            [['content'], 'string'],
            [['comment_id'], 'string', 'max' => 100],
            [['comment_id'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'auth_app_id' => 'Auth App ID',
            'auth_account_id' => 'Auth Account ID',
            'auth_item_id' => '项目',
            'auth_uid' => '评论者',
            'parent_id' => 'Parent ID',
            'comment_id' => 'Comment ID',
            'content' => '评论内容',
            'is_top' => '置顶',
            'digg_count' => 'Digg Count',
            'reply_count' => 'Reply Count',
            'create_time' => 'Create Time',
            'post_time' => 'Post Time',
        ];
    }
}
