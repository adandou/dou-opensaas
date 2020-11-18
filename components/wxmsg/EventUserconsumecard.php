<?php

namespace app\components\wxmsg;

use Yii;
use app\components\Func;
use app\models\WxCard;
use app\models\WxCode;
use app\models\WxUser;
use app\models\WxUserToObj;

/**
 * 用户核销卡券
 */
class EventUserconsumecard extends \app\models\WxMsg
{
    public function run(){
        $msg_arr = json_decode($this->msg_data, 1);
        $card_id = $msg_arr['CardId'];
        $code = $msg_arr['UserCardCode'];
        //核销来源。支持开发者统计API核销（FROM_API）、公众平台核销（FROM_MP）、卡券商户助手核销（FROM_MOBILE_HELPER）（核销员微信号）
        //$card_id = $msg_arr['ConsumeSource'];
        //门店名称，当前卡券核销的门店名称（只有通过自助核销和买单核销时才会出现）
        //$card_id = $msg_arr['LocationId'];
        //核销该卡券核销员的openid（只有通过卡券商户助手核销时才会出现）
        //$card_id = $msg_arr['StaffOpenId'];
        $wxCard = WxCard::getObjByCardId($card_id);
        if(!$wxCard){
            $wxCard = new WxCard();
            $wxCard->wx_aid = $this->wx_aid;
            $wxCard->eid = $this->eid;
            $wxCard->updateByWx();
        }

        $wxCode = WxCode::getObjByCode($code);
        if(!$wxCode){
            $wxCode = new WxCode();
            $wxCode->eid = $wxCard->eid;
            $wxCode->wx_aid = $wxCard->wx_aid;
            $wxCode->wx_card_id = $wxCard->id;
            $wxCode->wx_code = $code;
            $wxCode->wx_uid = $this->wxUser->id;
            $wxCode->get_time = 0;
            $wxCode->outer_id = 0;
            $wxCode->begin_time = 0;
            $wxCode->end_time = 0;
            $wxCode->wx_msg_id = $this->id;
        }
        $wxCode->consume_time = time();
        $wxCode->status = WxCode::STATUS_CONSUME_END;
        $wxCode->save(0);

    }
}
