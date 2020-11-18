<?php
require_once dirname(dirname(__DIR__)). '/Workerman/Autoloader.php';
use Workerman\Worker;
use Workerman\Protocols\Http\Response;
use Workerman\Connection\AsyncTcpConnection;
$worker = new Worker('http://localhost:8090');
$worker->onWorkerStart = function ($worker){

};
$worker->onMessage = function($connection, $request)
{
    try{
        $pathinfo = pathinfo($request->path());
        if(isset($pathinfo['extension']) && in_array(strtolower($pathinfo['extension']),['js','css','jpg','jpeg','png'])){
//            $connection->send(file_get_contents('.'.$request->path()));
            $connection->send((new Response())->withFile('.'.$request->path()));
        }
        $_SERVER['REQUEST_URI'] = $request->uri();
        $_SERVER['SCRIPT_NAME'] = '/'.$_SERVER['SCRIPT_NAME'];
        $_POST = $request->post();
        $_COOKIE = $request->cookie();
        $session = $request->session();
        $_SESSION = $session->all();
//            print_r($request->rawBuffer());
//        $connection->send(json_encode($_POST).json_encode($_COOKIE));exit;

//        print_r($request->uri());exit;
        // $request为请求对象，这里没有对请求对象执行任何操作直接返回hello给浏览器
        defined('YII_DEBUG') or define('YII_DEBUG', true);
        defined('YII_ENV') or define('YII_ENV', 'prod');
        //生产环境关闭报错
        if(!YII_DEBUG){
            ini_set('display_errors','Off');
            error_reporting(E_ALL & ~E_NOTICE);
        }

        require_once __DIR__ . '/../vendor/autoload.php';
        require_once __DIR__ . '/../vendor/yiisoft/yii2/Yii.php';

        $config = require __DIR__ . '/../config/web.php';
//        $config['components']['request']= [
//            'class'=>'app\components\Request',
//            'workermanRequest' => $request,
//            'enableCsrfValidation' => false,
//            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
//            'cookieValidationKey' => 'JrtuhgDR14pxps3DfvGTlDqO9Y81Vyix',
//        ];
//        throw new \Exception(json_encode($config),1);
        $appYii = new yii\web\Application($config);
//        throw new \Exception(1111,1);

        $appYii->urlManager->setBaseUrl('/');
        $appYii->urlManager->setHostInfo($request->host());
//        print_r($config);
        $appYii->request->setBaseUrl('/');
        $appYii->request->setScriptUrl('/');
//        $appYii->setBasePath(dirname(dirname(__FILE__)));
//        throw new \Exception(json_encode($_SERVER),1);
//        print_r($_SERVER);
        ob_start();
        $appYii->run();
        foreach ($_SESSION as $key => $val) {
            $session->set($key,$val);
        }
        $data = ob_get_contents();
        ob_clean();
        $response = new Response(200,[],$data);
        foreach($_COOKIE as $k => $v){
            $response->cookie($k,$v);
        }
        $connection->send($response);

    }catch (\Exception $e){
        $connection->send($e->getMessage().":".$e->getFile().":".$e->getLine());
    }
};
// 运行worker
Worker::runAll();