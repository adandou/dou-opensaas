<?php
namespace app\commands;

use Yii;
use yii\console\Controller;
use app\components\Func;
/*
 * crontab执行管理器，crontab里每分钟只执行这个文件，其他待执行文件在index方法里配置
 * */
class AdminController extends Controller
{
    public function actionIndex()
    {
        $cmd = Yii::$app->basePath . DIRECTORY_SEPARATOR . 'yii ';
        $date = date('Y-m-d');
        $day = date('d');
        $hour = date('H');
        $minute = date('i');
        $week = date('w');//本周几,周日为0
        //每分钟执行
        if (TRUE) {
            //批量发布
            Func::exec('auth-item/publish-items');

        }
        //每10分钟一次
        if($minute/10 == 1) {
//            Func::exec('wehub/pull-task');
        }
        //每小时执行
        if($minute == 59) {
            //定时刷新token
            Func::exec('auth-account/refresh-token');
            //定时刷新refresh_token
            Func::exec('auth-account/refresh-refresh-token');
        }
        //发送短信提醒
        if($hour == 12 && $minute == 30) {
        }
        //更新成交数据
        if($hour == 13 && $minute == 00) {
//            Func::exec('house/update-trans-data');

        }
        if($hour == 19 && $minute == 00) {
//            Func::exec('house/update-trans-data');
        }
        if($hour == 23 && $minute == 00) {
        }
        //凌晨0:01执行
        if($hour == 0 && $minute == 01) {
//            Func::exec('monther-table');//创建删除分表

        }
        //凌晨0:10执行
        if($hour == 0 && $minute == 10) {
            $date = date('Ymd',(time() - 86400));//昨天
//            Func::exec('stat/group',$date);//群统计
//            Func::exec('stat/user-rank',$date, 1);//群统计
//            Func::exec('btj/push-user-rank-all', '', 1);//团长热度同步到冰糖家
//            Func::exec('stat/wehub-user-all','', 1);//团长统计所有


        }
    }
}