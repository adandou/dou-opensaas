<?php

namespace app\components\wxmsg;

use Yii;
use app\components\Func;
use app\models\Company;
use app\models\WxUser;
use app\models\WxUserToObj;

/**
 * 用户领取卡券
 */
class EventUserdelcard extends \app\models\WxMsg
{
    public function run(){
        $msg_arr = json_decode($this->msg_data, 1);

        $card_id = $msg_arr['CardId'];
        $card_id = $msg_arr['UserCardCode'];
    }
}
