<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "auth_account".
 *
 * @property int $id
 * @property int $uid 所属用户ID
 * @property int $auth_app_id 平台ID
 * @property int $app_uid
 * @property string $open_id
 * @property string|null $nickname
 * @property string|null $avatar 头像
 * @property string $token
 * @property int $token_expire_time token失效时间
 * @property string $refresh_token refresh_token
 * @property int $refresh_token_expire_time refresh_token过期时间
 * @property int $refresh_token_get_nums refresh_token获取次数
 * @property int $all_expire_time 总过期时间
 * @property int $ctime
 */
class AuthAccount extends \yii\db\ActiveRecord
{
    //是否抖音企业号
    public function isDouyinCompany(){
        return in_array($this->authUser->role,[1,2,3]);
    }

    //评论回复
    public function commentReply(AuthItem $authItem,AuthComment $authComment,$content){
        return $this->authApp->getAppObj()->commentReply($this,$authItem,$authComment,$content);
    }
    //发布项目
    public function publishItem(AuthItem $authItem){
        return $this->authApp->getAppObj()->publishItem($this,$authItem);
    }
    //同步更新单个发布的数据
    public function syncItem(AuthItem $authItem){
        $this->authApp->getAppObj()->syncItem($this,$authItem);
    }
    //同步更新单个项目所有评论
    public function syncComments(AuthItem $authItem){
        $this->authApp->getAppObj()->syncComments($this,$authItem);
    }
    //同步账号的发布
    public function syncItems(){
        $this->authApp->getAppObj()->syncItems($this);
    }
    //同步用户粉丝列表
    public function syncUserFans(){
        $this->authApp->getAppObj()->syncUserFans($this);
    }
    //同步用户关注列表
    public function syncUserFollows(){
        $this->authApp->getAppObj()->syncUserFollows($this);
    }
    //同步用户信息
    public function updateAuthUserInfo(AuthUser $obj){
        $this->authApp->getAppObj()->updateAuthUserInfo($this,$obj);
    }
    public function getAuthUserInfo($open_id){
        $data = $this->authApp->getAppObj()->getAuthUserInfo($this,$open_id);
        return $data;
    }
    //刷新即将过期的token
    static public function refreshTokens($interval = 60){
        $start = time() -10;
        $end = time() + 10 + $interval;
        $objs = self::find()->andWhere("token_expire_time>".$start)->andWhere("token_expire_time<".$end)->all();
        foreach($objs as $obj){
            $obj->refreshToken();
        }
    }
    //刷新即将过期的refresh_token
    static public function refreshRefreshTokens($interval = 60){
        $start = time() -10;
        $end = time() + 10 + $interval;
        $objs = self::find()->andWhere("refresh_token_expire_time>".$start)->andWhere("token_expire_time<".$end)->all();
        foreach($objs as $obj){
            $obj->refreshRefreshToken();
        }
    }
    //刷新token
    public function refreshToken(){
        $this->authApp->getAppObj()->refreshToken($this);
    }
    //刷新refresh_token
    public function refreshRefreshToken(){
        $this->authApp->getAppObj()->refreshRefreshToken($this);
    }
    //关联用户
    public function getAuthUser(){
        return $this->hasOne(AuthUser::className(),['id'=>'app_uid']);
    }
    //关联应用
    public function getAuthApp(){
        return $this->hasOne(AuthApp::className(),['id'=>'auth_app_id']);
    }
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'auth_account';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['uid', 'auth_app_id', 'open_id', 'token', 'token_expire_time', 'refresh_token', 'refresh_token_expire_time', 'all_expire_time', 'ctime'], 'required'],
            [['uid', 'auth_app_id', 'app_uid', 'token_expire_time', 'refresh_token_expire_time', 'refresh_token_get_nums', 'all_expire_time', 'ctime'], 'integer'],
            [['open_id', 'nickname', 'avatar', 'token', 'refresh_token'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'uid' => 'Uid',
            'auth_app_id' => '所属应用',
            'app_uid' => 'App Uid',
            'open_id' => 'Open ID',
            'nickname' => '账号昵称',
            'avatar' => '账号头像',
            'token' => 'Token',
            'token_expire_time' => 'Token Expire Time',
            'refresh_token' => 'Refresh Token',
            'refresh_token_expire_time' => 'Refresh Token Expire Time',
            'refresh_token_get_nums' => 'Refresh Token Get Nums',
            'all_expire_time' => '授权过期',
            'ctime' => 'Ctime',
        ];
    }
}
