<?php

namespace app\components\wxmsg;


use Yii;
use app\components\Func;
use app\models\WxUserTag;
use app\models\WxAccount;
use app\models\WxCard;
use app\models\WxMemberCode;
/**
 * 会员卡激活事件推送
 */
class EventSubmitmembercarduserinfo extends \app\models\WxMsg
{
    public function run(){
        $msg_arr = json_decode($this->msg_data, 1);

        $card_id = $msg_arr['CardId'];
        $wxCard = WxCard::getObjByCardId($card_id);
        $code = $msg_arr['UserCardCode'];
        $wxCode = WxMemberCode::getObjByCode($code);
        $wxCode->activate();
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
                    'keyword2'=>['color'=>'#173177','value'=>'用户激活'],
                    'remark'=>['color'=>'#173177','value'=>''],
                ]
            ]);
        }
    }
}
