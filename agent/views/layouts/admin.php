<?php
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;
use yii\web\View;

/* @var $this \yii\web\View */
/* @var $content string */
AppAsset::register($this);
$this->registerJsFile('/static/js/common.js',['position'=>View::POS_BEGIN]);

?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
    <style>
        .cuscol{
            width:12%;
        }
    </style>
</head>
<body>

<?php $this->beginBody() ?>
    <div class="wrap">
        <?php
            NavBar::begin([
                'brandLabel' => '天天美',
                'brandUrl' => ['/site/index'],
                'options' => [
                    'class' => 'navbar-inverse',
                ],
            ]);
            echo Nav::widget([
                'options' => ['class' => 'navbar-nav navbar-right'],
                'items' => [
                    Yii::$app->user->isGuest ?
                        ['label' => '登录', 'url' => ['weixin/login']] :
                        ['label' => '退出 (' . Yii::$app->user->identity->nickname . ')',
                            'url' => ['/weixin/logout'],
                            'linkOptions' => ['data-method' => 'post']],
                ],
            ]);
            NavBar::end();
        ?>

        <div class="container-fluid">

            <div class="row">
                <div class="col-md-2 cuscol">
                    <?= Nav::widget([
                        'id' => 'admin-stat-table',
                        'options' => ['class' => 'cusnav'],
                        'items' => [
                            [
                                'label' => '平台应用',
                                'url' => ['/agent/auth-app/'],
                            ],
                            [
                                'label' => '授权账号',
                                'url' => ['/agent/auth-account/'],
                            ],
                            [
                                'label' => '发布管理',
                                'url' => ['/agent/auth-item/'],
                            ],
//                            [
//                                'label' => '平台',
//                                'url' => ['/agent/auth-platform/'],
//                            ],
//                            [
//                                'label' => '抖音授权账号',
//                                'url' => ['/agent/douyin-account/'],
//                            ],
//                            [
//                                'label' => '发布管理',
//                                'url' => ['/agent/douyin-item/'],
//                            ],
                            [
                                'label' => '评论管理',
                                'url' => ['/agent/auth-comment/'],
                            ],
                            [
                                'label' => '应用用户',
                                'url' => ['/agent/auth-user/'],
                            ],
                            [
                                'label' => '视频管理',
                                'url' => ['/agent/video/'],
                            ],
//                            [
//                                'label' => '统计',
//                                'url' => ['/agent/stat/'],
//                            ],
                            [
                                'label' => '帮助&客服',
                                'url' => ['/agent/help/'],
                            ],
                        ],
                    ]); ?>


                </div>
                <div class="col-md-10">
                    <?= Breadcrumbs::widget([
                        'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
                    ]) ?>
                    <?= $content ?>
                </div>
            </div>
        </div>

    </div>

    <footer class="footer">
        <div class="container">
            <p class="pull-left">&copy;  <?=Yii::$app->name."&nbsp;&nbsp;". date('Y') ?></p>
            <p class="pull-right">版权所属:北京昊识网络技术有限公司</p>
        </div>
    </footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
