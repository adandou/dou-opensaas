<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;


class WebhookController extends Controller
{
    public function actionIndex()
    {
        $dir = dirname(dirname(__FILE__));
        exec('cd '.$dir.' && git pull 2>&1', $output);
        exec('cd '.$dir.'/web && cp index.bak.php index.php 2>&1', $output);
        print_r($output);
        return 'success pc';
    }
}
