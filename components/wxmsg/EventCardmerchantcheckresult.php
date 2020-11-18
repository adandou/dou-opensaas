<?php

namespace app\components\wxmsg;

use app\models\WxAccount;
use app\models\WxUserTag;
use Yii;
use app\components\Func;
use app\models\Merchant;
use app\models\WxUser;

/**
 * 卡券子商户审核结果通知
 */
class EventCardmerchantcheckresult extends \app\models\WxMsg
{
    public function run(){
        $msg_arr = json_decode($this->msg_data, 1);
        $merchant_id = $msg_arr['MerchantId'];//子商户id,对于一个母商户公众号下唯一。创建卡券时需填入该id号
        $is_pass = $msg_arr['IsPass'];//是否通过审核,1为通过
        $merchant = Merchant::getObjByMerchantId($merchant_id);
        if(!$merchant) $merchant = new Merchant();
        $merchant->wx_merchant_id = $merchant_id;
        $merchant->state = $is_pass;
        $merchant->utime = time();
        if(!$merchant->save()) throw new \Exception(json_encode($merchant->getErrors(),JSON_UNESCAPED_UNICODE), 1);
        //审核成功时自动创建会员卡
        if($is_pass == 1){
            Func::exec('merchant/check-member-card',[$merchant->id]);
        }
        $passstr = ($msg_arr['IsPass']==1) ? '审核成功' : '审核失败';
        //向管理员发送审核结果模板消息
        $admins = WxUserTag::getAdminObjs();
        foreach($admins as $admin){
            WxAccount::pushTplMsg([
                'wx_aid'=> $this->wx_aid,
                'wx_uid'=> $admin->wx_uid,
                'template_id'=>'ICzjVHktwyk7WHjCACoVRD2VTn1sCY-jJ-ADV4MrnYQ',
                'data'=>[
                    'first'=>['color'=>'#173177','value'=>$merchant->brand_name],
                    'keyword1'=>['color'=>'#173177','value'=>'微信商户'],
                    'keyword2'=>['color'=>'#173177','value'=>$passstr],
                    'remark'=>['color'=>'#173177','value'=>@$msg_arr['Reason']],
                ]
            ]);
        }
    }
}
