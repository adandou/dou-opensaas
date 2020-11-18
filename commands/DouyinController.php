<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\commands;
use app\models\DouyinAccount;
use app\models\DouyinEmoji;
use app\models\DouyinItem;
use app\models\DouyinUser;
use Yii;
use yii\console\Controller;
use yii\console\ExitCode;
set_time_limit(0);
class DouyinController extends Controller
{
    //发布
    public function actionPublish($id){
        $obj = DouyinItem::findOne($id);
        $obj->publish();
    }
    //更新用户信息
    public function actionSyncUserinfo($id){
        $douyinUser = DouyinUser::findOne($id);
        $douyinUser->updateByDouyin();
    }
    //更新授权账户发布项目
    public function actionSyncItems($aid){
        $obj = DouyinAccount::findOne($aid);
        $cursor=0;
        $count = 10;
        do{
            $data = $obj->syncDouyinItem($count,$cursor);
            foreach ($data['list'] as $arr) {
                DouyinItem::syncByDouyinData($aid,$arr);
            }
            $cursor=$data['cursor'];

        }while($data['has_more']);
    }
    //更新一个项目
    public function actionSyncItem($id){
        $obj = DouyinItem::findOne($id);
        $obj->syncOne();
    }
    //拉取项目评论
    public function actionSyncItemComment($id){
        $obj = DouyinItem::findOne($id);
        $obj->syncComment();
    }
    //导入emoji
    public function actionImportEmoji(){
        $str = file_get_contents(__DIR__.'/douyin_emoji.json');
        $arr = json_decode($str,1);
        print_r($arr);
        foreach ($arr as $row){
            $obj = new DouyinEmoji();
            $obj->name = $row['name'];
            $obj->img = $row['img'];
            $obj->hide = $row['hide']??0;
            $obj->save();
        }
    }
}
