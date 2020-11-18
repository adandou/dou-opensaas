<?php

namespace app\components\wxmsg;

use Yii;
use app\components\Func;
use app\models\WxAccount;
use app\models\WxUserTag;
use app\models\WxCard;
use app\models\WxCode;
use app\models\WxMemberCode;

/**
 * 用户领取卡券
 */
class EventUsergetcard extends \app\models\WxMsg
{
    public function run(){
        $msg_arr = json_decode($this->msg_data, 1);
        $card_id = $msg_arr['CardId'];
        $code = $msg_arr['UserCardCode'];
        $outer_id = $msg_arr['OuterId'];
        $wxCard = WxCard::getObjByCardId($card_id);
        if(!$wxCard)throw new \Exception('商户卡券不存在', 1);
        if($wxCard->isMemberCard()){
            $wxCode = WxMemberCode::getObjByCode($code);
            if(!$wxCode)$wxCode = new WxMemberCode();
            $wxCode->wx_aid = $wxCard->wx_aid;
            $wxCode->merchant_id = $wxCard->merchant_id;
            $wxCode->wx_card_id = $wxCard->id;
            $wxCode->wx_code = $code;
            $wxCode->wx_uid = $this->wxUser->id;
            $wxCode->get_time = time();
            $wxCode->outer_id = $outer_id;
            $wxCode->state = WxMemberCode::STATE_NO_ACTIVATE;
            if(!$wxCode->save()) throw new \Exception(json_encode($wxCode->getErrors()), 1);
            //向管理员发送会员卡领取模板消息
            $admins = WxUserTag::getAdminObjs();
            foreach($admins as $admin){
                WxAccount::pushTplMsg([
                    'wx_aid'=> $this->wx_aid,
                    'wx_uid'=> $admin->wx_uid,
                    'template_id'=>'ICzjVHktwyk7WHjCACoVRD2VTn1sCY-jJ-ADV4MrnYQ',
                    'data'=>[
                        'first'=>['color'=>'#173177','value'=>$wxCard->card_title],
                        'keyword1'=>['color'=>'#173177','value'=>'微信卡券'],
                        'keyword2'=>['color'=>'#173177','value'=>'用户领取'],
                        'remark'=>['color'=>'#173177','value'=>''],
                    ]
                ]);
            }

        }else{
            $wxCode = new WxCode();
            $wxCode->wx_merchant_id = $wxCard->wx_merchant_id;
            $wxCode->wx_aid = $wxCard->wx_aid;
            $wxCode->wx_card_id = $wxCard->id;
            $wxCode->wx_code = $code;
            $wxCode->wx_uid = $this->wxUser->id;
            $wxCode->get_time = time();
            $wxCode->outer_id = $outer_id;
            $wxCode->begin_time = 0;
            $wxCode->end_time = 0;
            $wxCode->wx_msg_id = $this->id;
            $wxCode->updateByWx();
        }
        //$this->reply_content = '领卡成功请激活';
    }
}
