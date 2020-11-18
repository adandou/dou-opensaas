<?php

namespace app\models\auth_app;

use app\components\Func;
use app\models\AuthAccount;
use app\models\AuthAppAttr;
use app\models\AuthComment;
use app\models\AuthItem;
use app\models\AuthUser;
use app\models\AuthUserFans;
use app\models\DouyinEmoji;
use Yii;
use app\models\AuthApp;

class AuthAppDouyin extends AuthApp
{
    const URL_CONNECT = 'https://open.douyin.com/platform/oauth/connect';
    const URL_TOKEN = 'https://open.douyin.com/oauth/access_token';
    const URL_REFRESH_TOKEN = 'https://open.douyin.com/oauth/refresh_token/';
    const URL_REFRESH_REFRESH_TOKEN = 'https://open.douyin.com/oauth/renew_refresh_token/';

    const URL_USER_INFO = 'https://open.douyin.com/oauth/userinfo/';
    const URL_USER_FANS_LIST = 'https://open.douyin.com/fans/list/';
    const URL_USER_FOLLOW_LIST = 'https://open.douyin.com/following/list/';

    const URL_VIDEO_LIST = 'https://open.douyin.com/video/list/';
    const URL_VIDEO_DATA = 'https://open.douyin.com/video/data/';
    const URL_VIDEO_UPLOAD = 'https://open.douyin.com/video/upload/';
    const URL_VIDEO_UPLOAD_PART_INIT = 'https://open.douyin.com/video/part/init/';
    const URL_VIDEO_UPLOAD_PART_UPLOAD = 'https://open.douyin.com/video/part/upload/';
    const URL_VIDEO_UPLOAD_PART_COMPLETE = 'https://open.douyin.com/video/part/complete/';
    const URL_VIDEO_CREATE = 'https://open.douyin.com/video/create/';

    //抖音评论表情对照数据
    //https://sf1-hscdn-tos.pstatp.com/obj/ies-fe-bee/bee_prod/biz_181/bee_prod_181_bee_publish_1343.json
    //抖音表情图片地址
    //https://sf3-ttcdn-tos.pstatp.com/obj/ies-douyin-opencn/emoji/weixiao-3x.png
    //普通用户评论列表
    const DOUYIN_URL_COMMENT_LIST_COMMON = 'https://open.douyin.com/item/comment/list';
    //普通用户回复列表
    const DOUYIN_URL_COMMENT_REPLY_LIST_COMMON = 'https://open.douyin.com/item/comment/reply/list';
    //普通用户回复
    const DOUYIN_URL_COMMENT_REPLY_COMMON = 'https://open.douyin.com/item/comment/reply';
    //企业用户评论列表
    const DOUYIN_URL_COMMENT_LIST_COMPANY = 'https://open.douyin.com/video/comment/list';
    //企业用户回复列表
    const DOUYIN_URL_COMMENT_REPLY_LIST_COMPANY = 'https://open.douyin.com/video/comment/reply/list';
    //企业用户回复
    const DOUYIN_URL_COMMENT_REPLY_COMPANY = 'https://open.douyin.com/video/comment/reply';


    static public $scopes = [
        //视频权限
        'aweme.share',//抖音分享
        'video.create',
        'video.delete',
        'video.data',
        'video.list',
//        'toutiao.video.create',
//        'toutiao.video.data',
//        'xigua.video.data',
//        'xigua.video.create',
        //'im. share',
        //'video.search',
        //'video.search.comment',

        //用户权限
        'user_info',
        'following.list',
        'fans.list',
        //'login_id',
        'renew_refresh_token',
        //'mobile_alert',

        //互动权限
        'video.comment',//企业号评论
        'im',//企业私信
        'item.comment',

        //数据权限
        'hotsearch',
        'fans.data',
        'data.external.user',
        //'data.external.item',
        //'star_top_score_display',
        //'star_tops',
        //'star_author_score_display',
        //'data.external.sdk_share',

        //特殊权限
        //'share_with_source',
        //'poi.search',
        'micapp.is_legal',
    ];
    static public $roles = [
        'EAccountM'=>1,//普通企业号
        'EAccountS'=>2,//认证企业号
        'EAccountK'=>3,//品牌企业号
    ];

    //获取消息事件对象
    public function getMsgObj($data){
        if(empty($data)) return ;
        $msg_arr = json_decode($data, 1);

        $cname = ucfirst(strtolower(str_replace(['_',' '],'',$msg_arr['event'])));
        $cname = 'app\\models\\auth_app\\msg\\douyin\\'.$cname;
        if(!class_exists($cname))$cname = 'app\\models\\auth_app\\msg\\douyin\\Defaultmsg';
        $obj = new $cname();
        $obj->auth_app_id = $this->id;
        $obj->event = $msg_arr['event'];
        $obj->content = $data;
        if(!$obj->save()){
            throw new \Exception(json_encode($obj->getErrors(),JSON_UNESCAPED_UNICODE), 1);
        }
        return $obj;

    }
    //评论回复
    public function commentReply(AuthAccount $authAccount,AuthItem $authItem,$authComment,$content){
        if($authAccount->isDouyinCompany()){
            $url = self::DOUYIN_URL_COMMENT_REPLY_COMPANY;
        }else{
            $url = self::DOUYIN_URL_COMMENT_REPLY_COMMON;
        }
        $content = DouyinEmoji::inputContent($content);
        $query_arr = [
            'open_id'=>$authAccount->open_id,
            'access_token'=>$authAccount->token,
        ];
        $body_arr['item_id']=$authItem->item_id;
        $body_arr['content']=$content;
//        $authComment = $authComment->parentComment?:$authComment;
        if(!empty($authComment)){
            $body_arr['comment_id']=$authComment->comment_id;
        }
//        print_r($body_arr);exit;
        $body = json_encode($body_arr,JSON_UNESCAPED_UNICODE);
        $response = Func::postData(
            $url.'?'.http_build_query($query_arr),
            $body,5,
            ['Content-Type:application/json','Accept:application/json']);
        $result = self::checkResponse($response);
        $obj = new AuthComment();
        $obj->auth_app_id = $this->id;
        $obj->auth_account_id = $authAccount->id;
        $obj->auth_item_id = $authItem->id;
        $obj->auth_uid = $authAccount->app_uid;
        $obj->parent_id = $authComment->id;
        $obj->comment_id = $result['comment_id'];
        $obj->content = $content;
        $obj->create_time = time();
        $obj->post_time = time();
        if(!$obj->save()){
            throw new \Exception(json_encode($obj->getErrors()));
        }
        return $result;
    }

    //发布
    public function publishItem(AuthAccount $authAccount,AuthItem $authItem){
        //大于6M用分片上传,否则用普通上传
        if($authItem->video->size > 6000000){
            $data = $this->uploadPart($authAccount,$authItem);
        }else{
            $data = $this->upload($authAccount,$authItem);
        }
        $video_id = $data['video']['video_id'];
        $query_arr = [
            'open_id'=>$authAccount->open_id,
            'access_token'=>$authAccount->token,
        ];
        $body = json_encode(['video_id'=>$video_id,'text'=>$authItem->title],JSON_UNESCAPED_UNICODE);

        $response = Func::postData(
            self::URL_VIDEO_CREATE.'?'.http_build_query($query_arr),
            $body,5,
            ['Content-Type:application/json','Accept:application/json']);
        $data = self::checkResponse($response);
        return $data['item_id'];
    }
    //普通上传
    private function upload(AuthAccount $authAccount,AuthItem $authItem){
        $video = $authItem->video;
        $query_arr = [
            'open_id'=>$authAccount->open_id,
            'access_token'=>$authAccount->token,
        ];
        $content=$video->getPartData(0,6000000);
        $body_arr = Func::buildDouyinUploadData($content);


        $response = Func::postUploadData(
            self::URL_VIDEO_UPLOAD.'?'.http_build_query($query_arr),
            $body_arr['data'],0,
            ['Content-Type:multipart/form-data;boundary='.$body_arr['boundary']]);
        $data = self::checkResponse($response);
        print_r($response);
        return $data;

    }
    //分片上传
    private function uploadPart(AuthAccount $authAccount,AuthItem $authItem){
        $video = $authItem->video;
        $query_arr = [
            'open_id'=>$authAccount->open_id,
            'access_token'=>$authAccount->token,
        ];
        $body = '';

        $response = Func::postUploadData(
            self::URL_VIDEO_UPLOAD_PART_INIT.'?'.http_build_query($query_arr),
            $body,5,
            ['Content-Type:application/json']);
        $data = self::checkResponse($response);
//        print_r($data);exit;
        $upload_id = $data['upload_id'];
        $part=1;
        $part_size = 6000000;
        while($part){
            $start = ($part-1)*$part_size;
            if($start>=$video->size){
                break;
            }

            //如果剩余不足一个分片则一并上传
            $yu = $video->size - $start - $part_size;
            if($yu < $part_size){
                $part_size += $yu;
            }
            $content=$video->getPartData($start,$part_size);
            $body_arr = Func::buildDouyinUploadData($content);

//            print_r($body_arr);exit;
            $query_arr = [
                'open_id'=>$authAccount->open_id,
                'access_token'=>$authAccount->token,
                'upload_id'=>$upload_id,
                'part_number'=>$part,
            ];
//            $body = 'video='.$content;

            $response = Func::postUploadData(
                self::URL_VIDEO_UPLOAD_PART_UPLOAD.'?'.http_build_query($query_arr),
                $body_arr['data'],0,
                [
                    'Content-Type:multipart/form-data;boundary='.$body_arr['boundary'],
                ]);
            $data2 = self::checkResponse($response);
            $part++;

        }
        $query_arr = [
            'open_id'=>$authAccount->open_id,
            'access_token'=>$authAccount->token,
            'upload_id'=>$upload_id,
        ];

        $response = Func::postData(
            self::URL_VIDEO_UPLOAD_PART_COMPLETE.'?'.http_build_query($query_arr),
            $body,0,
            ['Content-Type:multipart/form-data']);
        $data = self::checkResponse($response);
        return $data;
    }
    //同步项目评论
    public function syncComments(AuthAccount $authAccount,AuthItem $authItem){
        $cursor=0;
        $count = 20;
        if($authAccount->isDouyinCompany()){
            $url = self::DOUYIN_URL_COMMENT_LIST_COMPANY;
            $url_reply = self::DOUYIN_URL_COMMENT_REPLY_LIST_COMPANY;
        }else{
            $url = self::DOUYIN_URL_COMMENT_LIST_COMMON;
            $url_reply = self::DOUYIN_URL_COMMENT_REPLY_LIST_COMMON;
        }
        //同步评论
        $query_arr = [
            'open_id'=>$authAccount->open_id,
            'access_token'=>$authAccount->token,
            'item_id'=>$authItem->item_id,
            'count'=>$count,
            'cursor'=>$cursor,
        ];
        $cols = ['auth_app_id','auth_account_id','auth_item_id','auth_uid','comment_id','content','is_top','digg_count','reply_count','post_time','create_time'];
        do{
            $response = Func::file_get_contents($url.'?'.http_build_query($query_arr));
            $data = self::checkResponse($response);
            $open_ids = [];
            $auth_user_rows = [];
            $rows = [];
            if(empty($data['has_more'])){
                break;
            }
//            print_r($data);exit;
            foreach ($data['list'] as $arr) {
                $open_ids[] = $arr['comment_user_id'];
                $auth_user_rows[]=[$this->id,$authAccount->id,$arr['comment_user_id'],time(),];
            }
            $sql = Yii::$app->db->getQueryBuilder()->batchInsert(AuthUser::tableName(), ['auth_app_id','auth_account_id','open_id','utime'], $auth_user_rows);
            $sql = str_replace('INSERT INTO','INSERT IGNORE INTO',$sql);
//            echo $sql;exit;
            Yii::$app->db->createCommand($sql)->execute();
            $auth_user_arr = AuthUser::find()->select(['open_id','id'])->where(['open_id'=>$open_ids])->indexBy('open_id')->asArray()->all();
//            print_r($auth_user_arr);exit;
            foreach ($data['list'] as $arr) {
                $rows[] = [
                    $this->id,
                    $authAccount->id,
                    $authItem->id,
                    $auth_user_arr[$arr['comment_user_id']]['id'],
                    $arr['comment_id'],
                    $arr['content'],
                    intval($arr['top']),
                    intval($arr['digg_count']),
                    intval($arr['reply_comment_total']),
                    intval($arr['create_time']),
                    time(),
                ];
            }
            $sql = Yii::$app->db->getQueryBuilder()->batchInsert(AuthComment::tableName(), $cols, $rows);
            $sql .= '  ON DUPLICATE KEY UPDATE 
                auth_uid=VALUES(auth_uid),
                is_top=VALUES(is_top),
                digg_count=VALUES(digg_count),
                reply_count=VALUES(reply_count)
            ';
            Yii::$app->db->createCommand($sql)->execute();

            $query_arr['cursor']=$data['cursor'];

        }while($data['has_more']);
        $query = AuthComment::find()->where(['auth_item_id'=>$authItem->id])->andWhere(['>','reply_count',0]);
        $comments = $query->all();
        foreach($comments as $comment){
            self::syncCommentReply($authAccount,$authItem,$comment);
        }

    }
    //更新项目评论的回复
    public function syncCommentReply(AuthAccount $authAccount,AuthItem $authItem,AuthComment $authComment){
        $cursor=0;
        $count = 20;
        if($authAccount->isDouyinCompany()){
            $url = self::DOUYIN_URL_COMMENT_REPLY_LIST_COMPANY;
        }else{
            $url = self::DOUYIN_URL_COMMENT_REPLY_LIST_COMMON;
        }
      //同步
        $query_arr = [
            'open_id'=>$authAccount->open_id,
            'access_token'=>$authAccount->token,
            'item_id'=>$authItem->item_id,
            'count'=>$count,
            'cursor'=>$cursor,
            'comment_id'=>$authComment->comment_id,
        ];
        $cols = ['auth_app_id','auth_account_id','auth_item_id','auth_uid','parent_id','comment_id','content','is_top','digg_count','reply_count','post_time','create_time'];
        do{
            $response = Func::file_get_contents($url.'?'.http_build_query($query_arr));
            $data = self::checkResponse($response);
            $open_ids = [];
            $auth_user_rows = [];
            $rows = [];
//            print_r($data['list']);exit;
            foreach ($data['list'] as $arr) {
                $open_ids[] = $arr['comment_user_id'];
                $auth_user_rows[]=[$this->id,$authAccount->id,$arr['comment_user_id'],time(),];
            }
            $sql = Yii::$app->db->getQueryBuilder()->batchInsert(AuthUser::tableName(), ['auth_app_id','auth_account_id','open_id','utime'], $auth_user_rows);
            $sql = str_replace('INSERT INTO','INSERT IGNORE INTO',$sql);
//            echo $sql;exit;
            Yii::$app->db->createCommand($sql)->execute();
            $auth_user_arr = AuthUser::find()->select(['open_id','id'])->where(['open_id'=>$open_ids])->indexBy('open_id')->asArray()->all();
//            print_r($auth_user_arr);exit;
            foreach ($data['list'] as $arr) {
                $rows[] = [
                    $this->id,
                    $authAccount->id,
                    $authItem->id,
                    $auth_user_arr[$arr['comment_user_id']]['id'],
                    $authComment->id,
                    $arr['comment_id'],
                    $arr['content'],
                    intval($arr['top']),
                    intval($arr['digg_count']),
                    intval($arr['reply_comment_total']),
                    intval($arr['create_time']),
                    time(),
                ];
            }
            $sql = Yii::$app->db->getQueryBuilder()->batchInsert(AuthComment::tableName(), $cols, $rows);
            $sql .= '  ON DUPLICATE KEY UPDATE 
                auth_uid=VALUES(auth_uid),
                parent_id=VALUES(parent_id),
                is_top=VALUES(is_top),
                digg_count=VALUES(digg_count),
                reply_count=VALUES(reply_count)
            ';
            Yii::$app->db->createCommand($sql)->execute();

            $query_arr['cursor']=$data['cursor'];

        }while($data['has_more']);
    }

    //同步单个发布项目
    public function syncItem(AuthAccount $authAccount, AuthItem $authItem){
        $query_arr = [
            'open_id'=>$authAccount->open_id,
            'access_token'=>$authAccount->token,
        ];
        $body = json_encode(['item_ids'=>[$authItem->item_id]],JSON_UNESCAPED_UNICODE);

        $response = Func::postData(
            self::URL_VIDEO_DATA.'?'.http_build_query($query_arr),
            $body,5,
            ['Content-Type:application/json','Accept:application/json']);
        $data = self::checkResponse($response);
        foreach($data['list'] as $arr){
            $authItem->title = $arr['title'];
            $authItem->cover = $arr['cover'];
            $authItem->show_url = $arr['share_url'];
            $authItem->is_top = intval($arr['is_top']);
            $authItem->comment_count = $arr['statistics']['comment_count']??0;
            $authItem->digg_count = $arr['statistics']['digg_count']??0;
            $authItem->download_count = $arr['statistics']['download_count']??0;
            $authItem->play_count = $arr['statistics']['play_count']??0;
            $authItem->share_count = $arr['statistics']['share_count']??0;
            $authItem->forward_count = $arr['statistics']['forward_count']??0;
            $authItem->post_time = $arr['create_time'];
            if(!$authItem->save()){
                throw new \Exception(json_encode($authItem->getErrors(),JSON_UNESCAPED_UNICODE));
            }

        }
        return true;

    }
    //同步用户发布的项目
    public function syncItems(AuthAccount $authAccount){
        $cursor=0;
        $count = 10;
        $query_arr = [
            'open_id'=>$authAccount->open_id,
            'access_token'=>$authAccount->token,
            'count'=>$count,
            'cursor'=>$cursor,
        ];
        do{
            $response = Func::file_get_contents(self::URL_VIDEO_LIST.'?'.http_build_query($query_arr));
            $data = self::checkResponse($response);
            foreach ($data['list'] as $arr) {
                $authItem = AuthItem::findOne(['item_id'=>$arr['item_id']]);
                if(empty($authItem)){
                    $authItem = new AuthItem();
                    $authItem->auth_app_id = $this->id;
                    $authItem->auth_account_id = $authAccount->id;
                    $authItem->item_id = $arr['item_id'];
                    $authItem->create_time = time();
                }
                $authItem->title = $arr['title'];
                $authItem->cover = $arr['cover'];
                $authItem->show_url = $arr['share_url'];
                $authItem->is_top = intval($arr['is_top']);
                $authItem->comment_count = $arr['statistics']['comment_count']??0;
                $authItem->digg_count = $arr['statistics']['digg_count']??0;
                $authItem->download_count = $arr['statistics']['download_count']??0;
                $authItem->play_count = $arr['statistics']['play_count']??0;
                $authItem->share_count = $arr['statistics']['share_count']??0;
                $authItem->forward_count = $arr['statistics']['forward_count']??0;
                $authItem->post_time = $arr['create_time'];
                if(!$authItem->save()){
                    throw new \Exception(json_encode($authItem->getErrors(),JSON_UNESCAPED_UNICODE));
                }
            }
            $query_arr['cursor']=$data['cursor'];

        }while($data['has_more']);
    }
    //同步粉丝列表
    public function syncUserFans(AuthAccount $authAccount){
        $cursor=0;
        $count = 20;
        $query_arr = [
            'open_id'=>$authAccount->open_id,
            'access_token'=>$authAccount->token,
            'count'=>$count,
            'cursor'=>$cursor,
        ];
        $cols = ['auth_app_id','auth_account_id','open_id','union_id','nickname','avatar','gender','city','province','country','utime'];
        do{
            $response = Func::file_get_contents(self::URL_USER_FANS_LIST.'?'.http_build_query($query_arr));
            $data = self::checkResponse($response);
            $open_ids = [];
            $rows = [];
            foreach ($data['list'] as $arr) {
                $open_ids[] = $arr['open_id'];
                $rows[] = [
                    $this->id,
                    $authAccount->id,
                    $arr['open_id'],
                    $arr['union_id'],
                    $arr['nickname'],
                    $arr['avatar'],
                    $arr['gender'],
                    $arr['city'],
                    $arr['province'],
                    $arr['country'],
                    time(),
                ];
            }
            $sql = Yii::$app->db->getQueryBuilder()->batchInsert(AuthUser::tableName(), $cols, $rows);
            $sql .= '  ON DUPLICATE KEY UPDATE 
                nickname=VALUES(nickname),
                avatar=VALUES(avatar)
            ';
            Yii::$app->db->createCommand($sql)->execute();
            $users = AuthUser::find()->select(['NAME_CONST("uid",'.$authAccount->app_uid.')','id',])->where(['open_id'=>$open_ids])->asArray()->all();
            $sql = Yii::$app->db->getQueryBuilder()->batchInsert(AuthUserFans::tableName(), ['auth_uid','fans_uid'], $users);
            $sql = str_replace('INSERT INTO','INSERT IGNORE INTO',$sql);
            Yii::$app->db->createCommand($sql)->execute();

            $query_arr['cursor']=$data['cursor'];

        }while($data['has_more']);

    }
    //同步关注列表
    public function syncUserFollows(AuthAccount $authAccount){
        $cursor=0;
        $count = 20;
        $query_arr = [
            'open_id'=>$authAccount->open_id,
            'access_token'=>$authAccount->token,
            'count'=>$count,
            'cursor'=>$cursor,
        ];
        $cols = ['auth_app_id','auth_account_id','open_id','union_id','nickname','avatar','gender','city','province','country','utime'];
        do{
            $response = Func::file_get_contents(self::URL_USER_FOLLOW_LIST.'?'.http_build_query($query_arr));
            $data = self::checkResponse($response);
            $open_ids = [];
            $rows = [];
            foreach ($data['list'] as $arr) {
                $open_ids[] = $arr['open_id'];
                $rows[] = [
                    $this->id,
                    $authAccount->id,
                    $arr['open_id'],
                    $arr['union_id'],
                    $arr['nickname'],
                    $arr['avatar'],
                    $arr['gender'],
                    $arr['city'],
                    $arr['province'],
                    $arr['country'],
                    time(),
                ];
            }
            $sql = Yii::$app->db->getQueryBuilder()->batchInsert(AuthUser::tableName(), $cols, $rows);
            $sql .= '  ON DUPLICATE KEY UPDATE 
                nickname=VALUES(nickname),
                avatar=VALUES(avatar)
            ';
            Yii::$app->db->createCommand($sql)->execute();
            $users = AuthUser::find()->select(['id','NAME_CONST("uid",'.$authAccount->app_uid.')'])->where(['open_id'=>$open_ids])->asArray()->all();
            $sql = Yii::$app->db->getQueryBuilder()->batchInsert(AuthUserFans::tableName(), ['auth_uid','fans_uid'], $users);
            $sql = str_replace('INSERT INTO','INSERT IGNORE INTO',$sql);
            Yii::$app->db->createCommand($sql)->execute();

            $query_arr['cursor']=$data['cursor'];

        }while($data['has_more']);

    }
    //更新用户信息
    public function updateAuthUserInfo(AuthAccount $obj,AuthUser $authUser){
        $arr = [
            'access_token'=>$obj->token,
            'open_id'=>$authUser->open_id,
        ];
        $response = Func::file_get_contents(self::URL_USER_INFO.'?'.http_build_query($arr));
        print_r($response);
        $data = self::checkResponse($response);
        $authUser->union_id = $data['union_id'];
        $authUser->avatar = $data['avatar'];
        $authUser->nickname = $data['nickname'];
        $authUser->gender = $data['gender'];
        $authUser->city = $data['city'];
        $authUser->province = $data['province'];
        $authUser->country = $data['country'];
        $authUser->role = self::$roles[$data['e_account_role']]??0;
        if(!$authUser->save()){
            throw new \Exception(json_encode($authUser->getErrors(),JSON_UNESCAPED_UNICODE));
        }

        return $authUser;
    }
    public function getAuthUserInfo(AuthAccount $obj){
        $arr = [
            'access_token'=>$obj->token,
            'open_id'=>$obj->open_id,
        ];
        $response = file_get_contents(self::URL_USER_INFO.'?'.http_build_query($arr));
        print_r($response);
        $data = self::checkResponse($response);
        return $data;
    }
    //刷新refresh_token
    public function refreshRefreshToken(AuthAccount $authAccount){
        $arr = [
            'client_key'=>$this->getAttr(AuthAppAttr::TYPE_DOUYIN_APP_KEY),
            'refresh_token'=>$authAccount->refresh_token,
        ];
        $response = Func::file_get_contents(self::URL_REFRESH_REFRESH_TOKEN.'?'.http_build_query($arr));
        $data = self::checkResponse($response);
        $authAccount->refresh_token = $data['refresh_token'];
        $authAccount->refresh_token_expire_time = (time() + $data['expires_in']);
        if(!$authAccount->save()){
            throw new \Exception(json_encode($authAccount->getErrors(),JSON_UNESCAPED_UNICODE));
        }

    }
    //刷新token
    public function refreshToken(AuthAccount $authAccount){
        $arr = [
            'client_key'=>$this->getAttr(AuthAppAttr::TYPE_DOUYIN_APP_KEY),
            'grant_type'=>'refresh_token',
            'refresh_token'=>$authAccount->refresh_token,
        ];
        $response = Func::file_get_contents(self::URL_REFRESH_TOKEN.'?'.http_build_query($arr));
        $data = self::checkResponse($response);
        $authAccount->token_expire_time = (time() + $data['expires_in']);
        if(!$authAccount->save()){
            throw new \Exception(json_encode($authAccount->getErrors(),JSON_UNESCAPED_UNICODE));
        }
    }
    //获取token
    public function getTokenByCode($code){
        $arr = [
            'client_key'=>$this->getAttr(AuthAppAttr::TYPE_DOUYIN_APP_KEY),
            'client_secret'=>$this->getAttr(AuthAppAttr::TYPE_DOUYIN_APP_SECRET),
            'code'=>$code,
            'grant_type'=>'authorization_code',
        ];
        $response = Func::file_get_contents(self::URL_TOKEN.'?'.http_build_query($arr));
        $data = self::checkResponse($response);
        return $data;
    }
    public function initToken($code){
        $token_arr = $this->getTokenByCode($code);
        $obj = AuthAccount::findOne(['open_id'=>$token_arr['open_id']]);
        if(empty($obj)){
            $obj = new AuthAccount();
            $obj->auth_app_id = $this->id;
            $obj->open_id = $token_arr['open_id'];
            $obj->ctime = time();
            $obj->all_expire_time = time()+180*86400;
        }
        $obj->uid = Yii::$app->user->id;
        $obj->token = $token_arr['access_token'];
        $obj->token_expire_time = (time() + $token_arr['expires_in']);
        $obj->refresh_token = $token_arr['refresh_token'];
        $obj->refresh_token_expire_time = (time() + $token_arr['refresh_expires_in']);
        if(!$obj->save()){
            throw new \Exception(json_encode($obj->getErrors(),JSON_UNESCAPED_UNICODE));
        }
        $authUser = AuthUser::find()->where(['open_id'=>$obj->open_id])->one();
        if(empty($authUser)){
            $authUser = new AuthUser();
            $authUser->auth_app_id = $obj->id;
            $authUser->auth_account_id = $obj->id;
            $authUser->open_id = $obj->open_id;
            $authUser->updateAuthUserInfo();

        }
        $obj->nickname = $authUser->nickname;
        $obj->avatar = $authUser->avatar;
        $obj->app_uid = $authUser->id;
        if(!$obj->save()){
            throw new \Exception(json_encode($obj->getErrors(),JSON_UNESCAPED_UNICODE));
        }

    }
    //创建授权跳转链接
    public function buildConnectUrl($state=''){
        $arr = [
            'client_key'=> $this->getAttr(AuthAppAttr::TYPE_DOUYIN_APP_KEY),
            'scope'=> implode(',',self::$scopes),
            'redirect_uri'=> 'https://www.ttmei.vip/oauth/douyin',
            'response_type'=> 'code',
            'state'=> $state,
        ];
        return self::URL_CONNECT.'?'.http_build_query($arr);
    }
    //检测并格式化抖音返回数据
    static public function checkResponse($response){
        $arr = json_decode($response,1);
        if(empty($arr)) throw new \Exception('抖音接口返回错误',20000);
        $data = $arr['data'];
        if(!isset($data['error_code'])) throw new \Exception('抖音接口返回错误',20000);
        if($data['error_code']!=0){
            Yii::error($response);
            throw new \Exception('抖音接口返回错误:'.$data['error_code'].":".$data['description'],20000);
        }
        return $data;
    }

}
