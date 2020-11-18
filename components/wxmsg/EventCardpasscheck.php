<?php

namespace app\components\wxmsg;

use app\models\WxMemberCard;
use app\models\WxMemberCode;
use Yii;
use app\components\Func;
use app\models\WxAccount;
use app\models\WxUserTag;
use app\models\WxCard;

/**
 * 卡券通过审核
 */
class EventCardpasscheck extends \app\models\WxMsg
{
    public function run(){
        $msg_arr = json_decode($this->msg_data, 1);
        $card_id = $msg_arr['CardId'];

        $wxCard = WxCard::getObjByCardId($card_id);
        if(!$wxCard) $wxCard = new WxCard();
        $wxCard->wx_aid = $this->wx_aid;
        $wxCard->card_id = $card_id;
        $wxCard->card_info = json_encode($wxCard->getWxCardInfo(),JSON_UNESCAPED_UNICODE);
        $wxCard->card_title = $wxCard->getCardTitleByCardInfo();
        $wxCard->ctime = time();
        $wxCard->status = WxCard::STATUS_OK;
        $wxCard->card_type = $wxCard->getCardTypeByCardInfo();
        if(!$wxCard->save()) throw new \Exception(json_encode($wxCard->getErrors(),JSON_UNESCAPED_UNICODE), 1);
        //向管理员发送审核结果模板消息
        $admins = WxUserTag::getAdminObjs();
        foreach($admins as $admin){
            WxAccount::pushTplMsg([
                'wx_aid'=> $this->wx_aid,
                'wx_uid'=> $admin->wx_uid,
                'template_id'=>'ICzjVHktwyk7WHjCACoVRD2VTn1sCY-jJ-ADV4MrnYQ',
                'data'=>[
                    'first'=>['color'=>'#173177','value'=>$wxCard->card_title],
                    'keyword1'=>['color'=>'#173177','value'=>'微信卡券'],
                    'keyword2'=>['color'=>'#173177','value'=>'审核通过'],
                    'remark'=>['color'=>'#173177','value'=>''],
                ]
            ]);
        }
    }
}
