<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use app\assets\AppAsset;
use yii\web\View;

AppAsset::register($this);
$this->clear();
////lhx 20171108 add
//$this->registerCssFile('https://weui.io/weui.css');
$this->registerCssFile('@web/static/css/weui.css');

//$this->registerCssFile('https://weui.io/example.css');
//$this->registerCssFile('@web/sttic/css/example.css');
//$this->registerCssFile('@web/static/css/master.css',['depends'=>'app\assets\AppAsset']);//lhx20171108

$this->registerJsFile('https://cdn.jsdelivr.net/npm/vue',['position'=>View::POS_HEAD]);
$this->registerJsFile('@web/static/js/zepto.min.js',['position'=>View::POS_HEAD]);
$this->registerJsFile('https://res.wx.qq.com/open/js/jweixin-1.3.2.js',['position'=>View::POS_HEAD]);
$this->registerJsFile('https://res.wx.qq.com/open/libs/weuijs/1.0.0/weui.min.js',['position'=>View::POS_HEAD]);


$this->registerJsFile('@web/static/js/common.js',['position'=>View::POS_HEAD]);
////lhx 20171108 add
//$this->registerJsFile('https://weui.io/example.js',['position'=>View::POS_HEAD]);


?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="cleartype" content="on">
    <!-- 启用360浏览器的极速模式(webkit) -->
    <meta name="renderer" content="webkit">
    <!-- 避免IE使用兼容模式 -->
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!-- 针对手持设备优化，主要是针对一些老的不识别viewport的浏览器，比如黑莓 -->
    <meta name="HandheldFriendly" content="true">
    <!-- 微软的老式浏览器 -->
    <meta name="MobileOptimized" content="320">
    <!-- uc强制竖屏 -->
    <meta name="screen-orientation" content="portrait">
    <!-- QQ强制竖屏 -->
    <meta name="x5-orientation" content="portrait">
    <!-- UC强制全屏 -->
    <meta name="full-screen" content="yes">
    <!-- QQ强制全屏 -->
    <meta name="x5-fullscreen" content="true">
    <!-- UC应用模式 -->
    <meta name="browsermode" content="application">
    <!-- QQ应用模式 -->
    <meta name="x5-page-mode" content="app">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>

        <?= $content ?>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
