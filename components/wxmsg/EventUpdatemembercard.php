<?php

namespace app\components\wxmsg;

use Yii;
use app\components\Func;
use app\models\Company;
use app\models\WxUser;
use app\models\WxUserToObj;

/**
 * 会员卡内容变动(积分变化)
 */
class EventUpdatemembercard extends \app\models\WxMsg
{
    public function run(){
        $msg_arr = json_decode($this->msg_data, 1);

        $card_id = $msg_arr['CardId'];
        $card_id = $msg_arr['UserCardCode'];
        $card_id = $msg_arr['ModifyBonus'];//变动的积分值
        $card_id = $msg_arr['ModifyBalance'];//变动的余额值
    }
}
