<?php

namespace app\components\wxmsg;

use Yii;
use app\components\Func;
use app\models\WxOpenAppAccount;

/**
 * 微信全网发布测试类".
 */
class PublishMsg extends \app\models\WxMsg
{
    public function run(){
        $msg_arr = json_decode($this->msg_data, 1);

        if($this->msg_type == 'event'){
            $this->reply_content = $this->event.'from_callback';
        }elseif($this->msg_type == 'text' && $this->getContent() == 'TESTCOMPONENT_MSG_TYPE_TEXT'){
            $this->reply_content = 'TESTCOMPONENT_MSG_TYPE_TEXT_callback';
        }elseif($this->msg_type == 'text' && (strpos($this->getContent(), 'QUERY_AUTH_CODE:') !== false)){
            $query_arr = explode(':', $this->getContent());
            $auth_code = $query_arr[1];
            $wxOpenAppAccount = WxOpenAppAccount::getObjByAuthCode($this->wxOpenApp, $auth_code);
            $wxOpenAppAccount->uid=0;
            $wxOpenAppAccount->save(false);
            $wxOpenAppAccount->sendMsgText($this->openid,$auth_code . '_from_api');
        }
    }
}
