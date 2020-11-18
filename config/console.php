<?php
Yii::setAlias('@tests', dirname(__DIR__) . '/tests');

$config = require(__DIR__ . '/web.php');
unset($config['id']);
unset($config['components']['user']);
unset($config['components']['request']);
unset($config['components']['errorHandler']);
unset($config['components']['session']);
unset($config['modules']['admin']);
unset($config['modules']['api']);
unset($config['modules']['goods']);
$config['bootstrap'] = ['log'];
$config['aliases'] = [
    '@bower' => '@vendor/bower-asset',
    '@npm'   => '@vendor/npm-asset',
    '@tests' => '@app/tests',
];
$config['controllerNamespace'] = 'app\commands';
$config['id'] = 'basic-console';

return $config;