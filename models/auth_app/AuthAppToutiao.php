<?php

namespace app\models\auth_app;

use app\components\Func;
use app\models\AuthAccount;
use app\models\AuthAppAttr;
use app\models\AuthItem;
use app\models\AuthUser;
use Yii;
use app\models\AuthApp;

class AuthAppToutiao extends AuthApp
{
    const URL_CONNECT = 'https://open.snssdk.com/oauth/authorize/';

    const URL_TOKEN = 'https://open.snssdk.com/oauth/access_token/';
    const URL_REFRESH_TOKEN = 'https://open.snssdk.com/oauth/refresh_token/';
    const URL_REFRESH_REFRESH_TOKEN = 'https://open.douyin.com/oauth/renew_refresh_token/';

    const URL_USER_INFO = 'https://open.snssdk.com/oauth/userinfo/';
    //头条视频用抖音的域名
    const URL_VIDEO_LIST = 'https://open.douyin.com/toutiao/video/list/';
    const URL_VIDEO_DATA = 'https://open.douyin.com/toutiao/video/data/';
    const URL_VIDEO_UPLOAD = 'https://open.douyin.com/toutiao/video/upload/';
    const URL_VIDEO_UPLOAD_PART_INIT = 'https://open.douyin.com/toutiao/video/part/init/';
    const URL_VIDEO_UPLOAD_PART_UPLOAD = 'https://open.douyin.com/toutiao/video/part/upload/';
    const URL_VIDEO_UPLOAD_PART_COMPLETE = 'https://open.douyin.com/toutiao/video/part/complete/';
    const URL_VIDEO_CREATE = 'https://open.douyin.com/toutiao/video/create/';



    static public $scopes = [
        'user_info',
        'toutiao.video.create',
        'toutiao.video.data',
    ];
//    static public $roles = [
//        'EAccountM'=>1,//普通企业号
//        'EAccountS'=>2,//认证企业号
//        'EAccountK'=>3,//品牌企业号
//    ];
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
            $authItem->show_url = $arr['share_url']??null;
            $authItem->is_top = intval($arr['is_top']??0);
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
            if(!isset($data['list'])) return ;
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
                $authItem->is_top = intval($arr['is_top']??0);
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

    //更新用户信息
    public function updateAuthUserInfo(AuthAccount $obj,AuthUser $authUser){
        $arr = [
            'access_token'=>$obj->token,
            'open_id'=>$authUser->open_id,
        ];
        $response = Func::file_get_contents(self::URL_USER_INFO.'?'.http_build_query($arr));
//        print_r($response);
        $data = self::checkResponse($response);
        $authUser->union_id = $data['union_id']??null;
        $authUser->avatar = $data['avatar'];
        $authUser->nickname = $data['nickname'];
        $authUser->gender = 0;
        $authUser->city = $data['city']??'';
        $authUser->province = $data['province']??'';
        $authUser->country = $data['country']??'';
        $authUser->role = 0;
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
            $authUser->auth_app_id = $obj->auth_app_id;
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
            'response_type'=> 'code',
            'scope'=> implode(',',self::$scopes),
            'redirect_uri'=> 'https://www.ttmei.vip/oauth/toutiao',
            'state'=> $state,
        ];
        return self::URL_CONNECT.'?'.http_build_query($arr);
    }
    //检测并格式化抖音返回数据
    static public function checkResponse($response){
        $arr = json_decode($response,1);
        if(empty($arr)) throw new \Exception('头条接口返回错误',20000);
        $data = $arr['data'];
        if(!isset($data['error_code'])) throw new \Exception('头条接口返回错误',20000);
        if($data['error_code']!=0){
            Yii::error($response);
            throw new \Exception('头条接口返回错误:'.$data['error_code'].":".$data['description'],20000);
        }
        return $data;
    }

}
