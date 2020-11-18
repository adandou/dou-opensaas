<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\commands;
use app\components\Func;
use Yii;

use app\models\AuthItem;
use yii\console\Controller;
set_time_limit(0);
class AuthItemController extends Controller
{
    //同步单个发布项目的数据
    public function actionSyncItem($id){
        $obj = AuthItem::findOne($id);
        $obj->syncItem();
    }

    //定时批量发布
    public function actionPublishItems(){
        //每分钟刷新一次
        $interval = 60;
        $start = time() -10;
        $end = time() + 10 + $interval;
//        $objs = self::find()->andWhere("post_time>".$start)->andWhere("post_time<".$end)->andWhere(['state'=>2])->all();
        $objs = AuthItem::find()->andWhere("post_time<".$end)->andWhere(['state'=>2])->all();
        foreach($objs as $obj){
            Func::exec('auth-item/publish-item',[$obj->id]);
        }
    }
    //单项发布
    public function actionPublishItem($id){
        $obj = AuthItem::findOne($id);
        $obj->publishItem();
    }
    //同步项目所有评论
    public function actionSyncComments($id){
        $obj = AuthItem::findOne($id);
        $obj->syncComments();

    }
}
