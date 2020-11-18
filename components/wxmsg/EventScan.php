<?php

namespace app\components\wxmsg;

use Yii;
use app\components\Func;
use app\models\WxQr;
use app\models\WxUser;
use app\models\WxUserToObj;

/**
 * 微信消息类".
 */
class EventScan extends \app\models\WxMsg
{
    public function run(){
        WxQr::runscan($this);
    }
}
