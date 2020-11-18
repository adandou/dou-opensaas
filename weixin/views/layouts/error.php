<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\helpers\Url;

$this->title = '错误';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="page">
    <div class="weui-msg">
        <div class="weui-msg__icon-area"><i class="weui-icon-warn weui-icon_msg"></i></div>
        <div class="weui-msg__text-area">
            <h2 class="weui-msg__title">操作失败</h2>
            <p class="weui-msg__desc"><?=$e->getMessage()?></p>
        </div>
        <div class="weui-msg__opr-area">
            <p class="weui-btn-area">
                <a href="javascript:history.back();" class="weui-btn weui-btn_primary">返回</a>
<!--                <a href="javascript:history.back();" class="weui-btn weui-btn_default">辅助操作</a>-->
            </p>
        </div>
        <div class="weui-msg__extra-area">
            <div class="weui-footer">
                <p class="weui-footer__links">
                    <a href="javascript:void(0);" class="weui-footer__link">每天美VIP</a>
                </p>
                <p class="weui-footer__text">Copyright &copy; 2017-2018 meitianmei.vip</p>
            </div>
        </div>
    </div>
</div>


<script type="text/javascript" class="searchbar js_show">
    wx.ready(function(){
    });
</script>
