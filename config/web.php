<?php
$config = [
    'language'=>'zh-CN',
    'timeZone' => 'Asia/Shanghai',
    'id' => 'basic',
    'name' => '天天美',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'components' => [
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'cache' => false,
            'rules' => [
                'oauth/<id:\d+>/msg/?'=> 'oauth/msg',
            ],
        ],
        'request' => [
            'enableCsrfValidation' => false,
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'xxxx',
        ],
        'assetManager'=>[
            'bundles'=>[
                'yii\web\JqueryAsset'=>[
                    //'sourcePath' => null,'js' => [],不使用默认jquery
                    'jsOptions'=>[
                        'position'=>\yii\web\View::POS_HEAD, //head中
                    ]
                ],
                'yii\web\YiiAsset'=>[
                    //'sourcePath' => null,'js' => [],不使用默认
                    'jsOptions'=>[
                        'position'=>\yii\web\View::POS_HEAD, //head中
                    ]
                ],
                'yii\bootstrap\BootstrapPluginAsset'=>[
                    //'sourcePath' => null,'js' => [],不使用默认
                    'jsOptions'=>[
                        'position'=>\yii\web\View::POS_HEAD, //head中
                    ]
                ],
            ]
        ],

//        'cache' => [
//            'class' => 'yii\caching\FileCache',
//        ],
        'user' => [
            'identityClass' => 'app\models\WxUser',
            'enableAutoLogin' => false,
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            // send all mails to a file by default. You have to set
            // 'useFileTransport' to false and configure a transport
            // for the mailer to send real emails.
            'useFileTransport' => true,
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
//        'session' => [
//            'class' => 'yii\redis\Session',
//            'redis' => 'redis',
//        ],
        /*
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
            ],
        ],
        */
    ],
    'modules' => [
        'admin' => [
            'class' => 'app\modules\admin\Module',
            'defaultRoute' => 'agent',
        ],
        'agent' => [
            'class' => 'app\modules\agent\Module',
            'defaultRoute' => 'auth-account',
        ],
        'api' => [
            'class' => 'app\modules\api\Module',
            'defaultRoute' => 'default',
        ],
//        'weixin' => [
//            'class' => 'app\modules\weixin\Module',
//            'defaultRoute' => 'default',
//        ],
    ],
    'params' => [
        'oss_access_key' => 'xxxx',
        'oss_access_secret' => 'xxxx',
        'oss_endpoint' => 'oss-cn-beijing.aliyuncs.com',
    ],
];
//测试
if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];
    $config['components']['db'] = [
        'class' => 'yii\db\Connection',
        'dsn' => 'mysql:host=xxx;dbname=douyin_ttmei',
        'username' => 'xxx',
        'password' => 'xxx',
        'charset' => 'utf8mb4',
    ];
    $config['components']['redis'] = [
        'class' => 'yii\redis\Connection',
        'hostname' => 'xxx',
        'port' => 6379,
        'password' => 'xxx',
        'database' => 6,
    ];
}else{
    //生产
    $config['components']['db'] = [
        'class' => 'yii\db\Connection',
        'dsn' => 'mysql:host=localhost;dbname=douyin_ttmei',
        'username' => 'xxx',
        'password' => 'xxx',
        'charset' => 'utf8mb4',
    ];
    $config['components']['redis'] = [
        'class' => 'yii\redis\Connection',
        'hostname' => 'localhost',
        'port' => 6379,
        'password' => 'xxx',
        'database' => 6,
    ];
}


return $config;
