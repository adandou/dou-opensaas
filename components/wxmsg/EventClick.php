<?php

namespace app\components\wxmsg;

use Yii;
use app\components\Func;

/**
 * 微信消息类".
 */
class EventClick extends \app\models\WxMsg
{
    public function run(){
        $msg_arr = json_decode($this->msg_data, 1);
        switch($msg_arr['EventKey']){
            case 'lianxi':
                $this->reply_content .= "邮箱:lihaixin@meitianmei.vip\n\r微信号:tianshihaixin";
                break;
            default:
                break;
        }
    }
}
