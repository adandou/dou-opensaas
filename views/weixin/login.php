<?php

/* @var $this yii\web\View */

$this->title = 'My Yii Application';
?>
<div class="site-index">

    <div class="jumbotron">
        <p>微信扫码登录</p>
        <p><img src="<?=$wxQr->getPic()?>"></p>
    </div>

    <div class="body-content">
    </div>
</div>
<script>
    var code="<?=$wxQr->scene_value?>";
    setInterval(function(){
        $.post('/weixin/check-login',{code:code},function (req) {
            if(req == 1){
                window.location = '/agent/auth-app';
            }
        });

    },2000);
</script>