<?php

namespace app\components\wxmsg;

use Yii;
use app\components\Func;
use app\models\Company;
use app\models\WxUser;
use app\models\WxUserToObj;

/**
 * 卡券快速买单并核销
 */
class EventUserpayfrompaycell extends \app\models\WxMsg
{
    public function run(){
        $msg_arr = json_decode($this->msg_data, 1);

        $card_id = $msg_arr['CardId'];
        $card_id = $msg_arr['UserCardCode'];
        //微信支付交易订单号（只有使用买单功能核销的卡券才会出现）
        $card_id = $msg_arr['TransId'];
        //门店名称，当前卡券核销的门店名称（只有通过自助核销和买单核销时才会出现）
        //$card_id = $msg_arr['LocationId'];
        //实付金额，单位为分
        //$card_id = $msg_arr['Fee'];
        //应付金额，单位为分
        //$card_id = $msg_arr['OriginalFee'];
    }
}
