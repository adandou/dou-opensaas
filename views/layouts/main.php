<?php

/* @var $this \yii\web\View */
/* @var $content string */

use app\widgets\Alert;
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;

AppAsset::register($this);
$this->registerJsFile('/static/js/common.js',['position'=>\yii\web\View::POS_BEGIN]);

?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="keywords" content="视频分发|多账号管理|多账号运营|账号矩阵|抖音多账号|抖音矩阵|快手多账号|快手矩阵">
    <meta name="description" content="天天美视频分发多账号矩阵运营管理系统，适用于抖音、快手等视频分享平台,尤其适合多个账号、账号矩阵的管理">
    <?php $this->registerCsrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>

<div class="wrap">
    <?php
    NavBar::begin([
        'brandLabel' => Yii::$app->name,
        'brandUrl' => Yii::$app->homeUrl,
        'options' => [
            'class' => 'navbar-inverse navbar-fixed-top',
        ],
    ]);
    $nav_arr = [
        'options' => ['class' => 'navbar-nav navbar-right'],
        'items' => [
            ['label' => '首页', 'url' => ['/site/index']],
//            ['label' => '关于我们', 'url' => ['/site/about']],
//            ['label' => '联系我们', 'url' => ['/site/contact']],
            ]
    ];
    if(Yii::$app->user->isGuest){
        $nav_arr['items'][] =['label' => '微信登录', 'url' => ['/weixin/login']];
//        $nav_arr['items'][] =['label' => '代理商登录', 'url' => ['/site/login']];
    }else{
        $nav_arr['items'][] =['label' => '后台', 'url' => ['/agent/']];
//        $nav_arr['items'][] =['label' => '代理商后台', 'url' => ['/agent/']];
        $nav_arr['items'][] ='<li>'
            . Html::beginForm(['/weixin/logout'], 'post')
            . Html::submitButton(
                '退出 (' . Yii::$app->user->identity->nickname . ')',
                ['class' => 'btn btn-link logout']
            )
            . Html::endForm()
            . '</li>';

    }
    echo Nav::widget($nav_arr);
    NavBar::end();
    ?>

    <div class="container">
        <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>
        <?= Alert::widget() ?>
        <?= $content ?>
    </div>
</div>

<footer class="footer">
    <div class="container">
        <p class="pull-left">&copy;  <?=Yii::$app->name."&nbsp;&nbsp;". date('Y') ?> 京ICP备17069454号-1</p>

        <p class="pull-right">版权所属:北京昊识网络技术有限公司</p>
    </div>
</footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
