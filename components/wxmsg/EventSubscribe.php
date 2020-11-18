<?php

namespace app\components\wxmsg;

use Yii;
use app\components\Func;
use app\models\WxQr;
use app\models\WxUserToObj;

/**
 * 微信关注事件类".
 */
class EventSubscribe extends \app\models\WxMsg
{
    public function run(){
        $this->wxUser->updateUserInfo();
        $this->reply_content .= "欢迎关注本公众号\n\r";
        WxQr::runscan($this);
    }
}
