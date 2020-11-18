<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\helpers\Url;

$this->title = '选择商户';
$this->params['breadcrumbs'][] = $this->title;
?>
    <div class="page">
        <div class="page__bd">
            <div class="weui-cells">
<?php foreach($objs as $obj){ ?>
                <a href="<?=Url::current(['merchant_id'=>$obj->id])?>" class="weui-media-box weui-media-box_appmsg">
                    <div class="weui-media-box__hd">
                        <img class="weui-media-box__thumb" src="<?=$obj->getLogoSrc()?>" alt="">
                    </div>
                    <div class="weui-media-box__bd">
                        <h4 class="weui-media-box__title"><?=$obj->brand_name?></h4>
                        <p class="weui-media-box__desc">最后更新时间:<?=date('Y-m-d H:i:s',$obj->utime)?></p>
                    </div>
                </a>

<?php }?>
            </div>
        </div>
    </div>

