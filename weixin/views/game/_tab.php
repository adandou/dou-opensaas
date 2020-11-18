<?php

use yii\helpers\Html;
use yii\helpers\Url;

?>
    <div class="weui-tab"">
        <div class="weui-navbar">
            <div class="weui-navbar__item <?=@$selected1?>" onclick="javascript:window.location='<?=Url::to(['/weixin/merchant-staff/','merchant_id'=>$merchant_id])?>';">
                全体员工
            </div>
            <div class="weui-navbar__item <?=@$selected2?>" onclick="javascript:window.location='<?=Url::to(['/weixin/merchant-staff/tag','merchant_id'=>$merchant_id])?>';">
                员工分组
            </div>
            <div class="weui-navbar__item <?=@$selected3?>" onclick="javascript:window.location='<?=Url::to(['/weixin/merchant-staff/add','merchant_id'=>$merchant_id])?>';">
                新增员工
            </div>
        </div>
        <div class="weui-tab__panel">
        </div>
    </div>
<script type="text/javascript" class="searchbar js_show">
    wx.ready(function(){
    });
</script>
