<?php

namespace app\modules\agent\controllers;

use app\components\Func;
use app\models\AuthAccount;
use app\models\AuthComment;
use app\models\AuthItem;
use app\models\Video;
use app\modules\agent\AdminController;
use Yii;
use AlibabaCloud\Sts\Sts;
use AlibabaCloud\Client\AlibabaCloud;

class AjaxController extends AdminController
{
    //回复评论
    public function actionCommentReply(){
        $id = Yii::$app->request->post('comment_id',0);
        $content = Yii::$app->request->post('content','');
        if(empty($content)){
            throw new \Exception('内容为空', 1);
        }
        $obj = AuthComment::findOne($id);
        if(empty($obj)){
            throw new \Exception('参数错误', 1);
        }
        if($obj->authAccount->uid != Yii::$app->user->id){
            throw new \Exception('无权限', 1);
        }
        $obj->commentReply($content);
        return $this->success();
    }
    /**
     * 同步更新用户发布的所有项目
     * 20200915
     */
    public function actionSyncItems()
    {
        $id = Yii::$app->request->post('aid',0);
        $obj = AuthAccount::findOne($id);
        if(empty($obj)){
            throw new \Exception('参数错误', 1);
        }
        if($obj->uid != Yii::$app->user->id){
            throw new \Exception('无权限', 1);
        }
        Func::exec('auth-account/sync-items',[$id]);
        return $this->success();
    }
    /**
     * 同步更新用户的关注和粉丝
     * 20200915
     */
    public function actionSyncFollowFans()
    {
        $id = Yii::$app->request->post('aid',0);
        $obj = AuthAccount::findOne($id);
        if(empty($obj)){
            throw new \Exception('参数错误', 1);
        }
        if($obj->uid != Yii::$app->user->id){
            throw new \Exception('无权限', 1);
        }
        Func::exec('auth-account/sync-follow-fans',[$id]);
        return $this->success();
    }
    /**
     * 同步更新单个项目
     * 20200915
     */
    public function actionSyncItem()
    {
        $item_id = Yii::$app->request->post('id',0);
        $obj = AuthItem::findOne($item_id);
        if(empty($obj)){
            throw new \Exception('参数错误', 1);
        }
        if($obj->authAccount->uid != Yii::$app->user->id){
            throw new \Exception('无权限', 1);
        }
        Func::exec('auth-item/sync-item',[$item_id]);
        return $this->success();
    }
    /**
     * 发布项目
     * 20200915
     */
    public function actionPublish(){
        $str = file_get_contents("php://input");
        $arr = json_decode($str,1);
        foreach ($arr as $row){
            if(empty($row)) continue;
            $obj = new AuthItem();
            $obj->auth_app_id = $row['auth_app_id'];
            $obj->auth_account_id = $row['auth_account_id'];
            $obj->video_id = $row['video_id'];
            $obj->title = $row['title'];
            $obj->create_time = time();
            $obj->post_time = $row['timing'] == 1 ? intval(strtotime($row['post_time'])) : time();
            $obj->timing = $row['timing'];
            $obj->state = 2;
            if(!$obj->save()){
                throw new \Exception(json_encode($obj->getErrors()));
            }
//            Func::exec('douyin-item/publish',[$obj->id]);
        }
        return $this->success();
    }
    /**
     * 同步单个项目评论
     * 20200920
     */
    public function actionSyncComment()
    {
        $item_id = Yii::$app->request->post('id',0);
        $authItem = AuthItem::findOne($item_id);
        if(empty($authItem)){
            throw new \Exception('参数错误', 1);
        }
        if($authItem->authAccount->uid != Yii::$app->user->id){
            throw new \Exception('无权限', 1);
        }
        Func::exec('auth-item/sync-comments',[$item_id]);
        return $this->success();
    }


    //上传视频信息
    public function actionUploadVideoInfo(){
        $store = Yii::$app->request->post('store',1);
        $filename = Yii::$app->request->post('filename','');
        $title = Yii::$app->request->post('title','');
        $size = Yii::$app->request->post('size',0);
        $duration = Yii::$app->request->post('duration',0);
        $wx_uid = Yii::$app->user->id;
        $video = Video::makeObjByUploadInfo($wx_uid,$store,$filename,$title,$size,$duration);
        return $this->success(['filename'=>$video->getStoreFile(1)]);
    }
//    public function actionGetComment($comment_id){
//        $comment = DouyinComment::findOne($comment_id);
//        if(Yii::$app->user->id!=$comment->douyinItem->douyinAccount->wx_uid){
//            throw new \Exception('无权限', 1);
//        }
//        return $this->success(['content'=>$comment->content]);
//    }
//    public function actionReplyComment(){
//        echo __LINE__.PHP_EOL;
//        $comment_id = Yii::$app->request->post('comment_id',0);
//        $content = Yii::$app->request->post('content','');
//        $comment = DouyinComment::findOne($comment_id);
//        echo __LINE__.PHP_EOL;
//        if(Yii::$app->user->id!=$comment->douyinItem->douyinAccount->wx_uid){
//            throw new \Exception('无权限', 1);
//        }
//        echo __LINE__.PHP_EOL;
//        if(empty($content)){
//            throw new \Exception('回复不能为空', 1);
//        }
//        echo __LINE__.PHP_EOL;
//        $comment->reply($content);
//        echo __LINE__.PHP_EOL;
//        return $this->success();
//    }
//    /**
//     * 同步用户的发布项目
//     */
//    public function actionSyncUserItem()
//    {
//        $douyin_account_id = Yii::$app->request->post('aid',0);
//        $douyinAccount = DouyinAccount::findOne($douyin_account_id);
//        if(empty($douyinAccount)){
//            throw new \Exception('参数错误', 1);
//        }
//        if($douyinAccount->wx_uid != Yii::$app->user->id){
//            throw new \Exception('无权限', 1);
//        }
//        Func::exec('auth-account/sync-items',[$douyin_account_id]);
//        return $this->success();
//    }
    //阿里云STS授权
    public function actionAliSts(){
        //构建阿里云client时需要设置AccessKey ID和AccessKey Secret
        AlibabaCloud::accessKeyClient('LTAI4FzwHxyEY6Sxwjk1yxmh', 'mkjTYDOIQeH5ZSIZWyX5GON3e0bjCE')
            ->regionId('cn-beijing')
            ->asDefaultClient();

        $res = Sts::v20150401()
            ->assumeRole()
            //指定角色ARN
            ->withRoleArn('acs:ram::1437363577098321:role/oss-sts')
            //RoleSessionName即临时身份的会话名称，用于区分不同的临时身份
            ->withRoleSessionName('external-username')
//            ->withRoleSessionName(Yii::$app->user->id)
            //设置权限策略以进一步限制角色的权限
            //以下权限策略表示拥有所有OSS的只读权限
            ->withPolicy('{
             "Statement":[
                {
                     "Action":
                 [
                     "oss:*"
                     ],
                      "Effect": "Allow",
                      "Resource": "*"
                }
                   ],
          "Version": "1"
        }')
            ->connectTimeout(60)
            ->timeout(65)
            ->request();
        $arr = $res->get('Credentials');
//        print_r($arr);
        return $this->success($arr);
    }
    public function init()
    {
        parent::init(); // TODO: Change the autogenerated stub
        set_exception_handler([$this, 'error']);

    }

    //统一报错AJAX
    public function error(\Throwable $e){
        $errmsg = $e->getMessage();
        if(YII_DEBUG){
            $errmsg.=$e->getFile().$e->getLine();
        }
        else{
            Yii::error($errmsg.$e->getFile().$e->getLine());
        }
        echo json_encode(['errcode'=>$e->getCode(),'errmsg'=>$errmsg],JSON_UNESCAPED_UNICODE);
//        return json_encode(['errcode'=>$e->getCode(),'errmsg'=>$errmsg],JSON_UNESCAPED_UNICODE);
    }
    //统一成功JAX
    public function success($arr = []){

//        header("Access-Control-Allow-Origin: *");
//        header("Access-Control-Allow-Methods: POST, GET, OPTIONS, PUT, DELETE");
//        header('Access-Control-Allow-Headers:x-requested-with,content-type');
        return json_encode(array_merge(['errcode'=>0,'errmsg'=>'','result'=>$arr]),JSON_UNESCAPED_UNICODE);
    }

}
