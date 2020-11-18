<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\commands;
use app\models\AuthAccount;
use Yii;
use yii\console\Controller;
set_time_limit(0);
class AuthAccountController extends Controller
{
    //同步授权账户的关注和粉丝
    public function actionSyncFollowFans($auth_account_id){
        $obj = AuthAccount::findOne($auth_account_id);
        $obj->syncUserFollows();
        $obj->syncUserFans();
    }
    //同步授权账户发布项目
    public function actionSyncItems($auth_account_id){
        $obj = AuthAccount::findOne($auth_account_id);
        $obj->syncItems();
    }
    //同步授权账户粉丝列表
    public function actionSyncUserFans($auth_account_id){
        $obj = AuthAccount::findOne($auth_account_id);
        $obj->syncUserFans();
    }
    //同步授权账户关注列表
    public function actionSyncUserFollows($auth_account_id){
        $obj = AuthAccount::findOne($auth_account_id);
        $obj->syncUserFollows();
    }

    //定时刷新快过期的token
    public function actionRefreshToken(){
        AuthAccount::refreshTokens(3600);
    }
    //定时刷新快过期的refresh_token
    public function actionRefreshRefreshToken(){
        AuthAccount::refreshRefreshTokens(3600);
    }
}
