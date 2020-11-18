<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\helpers\Url;

$this->title = '员工列表';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="page">
    <div class="page__bd">
        <?= $this->render('_tab', [
            'merchant_id' => 1,
            'selected1' => 'weui-bar__item_on',
        ]) ?>
        <div class="weui-cells">
            <?php foreach($objs as $obj){ ?>
                <a href="<?=Url::to(['/weixin/merchant/update','id'=>$obj->id])?>" class="weui-media-box weui-media-box_appmsg">
                    <div class="weui-media-box__hd">
                        <img class="weui-media-box__thumb" src="<?=$obj->head?>" alt="">
                    </div>
                    <div class="weui-media-box__bd">
                        <h4 class="weui-media-box__title"><?=$obj->nickname?></h4>
                        <p class="weui-media-box__desc">最后更新时间:<?=$obj->phone?></p>
                    </div>
                </a>

            <?php }?>
        </div>
    </div>
</div>

<script type="text/javascript" class="searchbar js_show">
    wx.ready(function(){
    });
</script>
