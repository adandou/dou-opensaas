<?php

namespace app\controllers;

use app\models\DouyinApp;
use app\models\WxAccount;
use app\models\WxQr;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;

use AlibabaCloud\Sts\Sts;
use AlibabaCloud\Client\AlibabaCloud;
use AlibabaCloud\Sts\V20150401\AssumeRole;
use AlibabaCloud\Client\Exception\ServerException;
use AlibabaCloud\Client\Exception\ClientException;
class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }
    public function actionSts2(){
        $url ='https://sts-vpc.cn-beijing.aliyuncs.com';
        $arr = [
            'Action'=>'AssumeRole',
            'RoleArn'=>'acs:ram::1437363577098321:role/oss-sts',
            'RoleSessionName'=>'sid',
            'DurationSeconds'=>'3600',
            'Policy'=>'',
        ];
        $url = $url.'?'.http_build_query($arr);
        echo $url.PHP_EOL;exit;
        $res = file_get_contents($url);
        print_r($res);
    }
    public function actionSts(){
        //构建阿里云client时需要设置AccessKey ID和AccessKey Secret
        AlibabaCloud::accessKeyClient('xxx', 'xxx')
            ->regionId('cn-beijing')
            ->asDefaultClient();

        $res = Sts::v20150401()
            ->assumeRole()
            //指定角色ARN
            ->withRoleArn('xxx')
            //RoleSessionName即临时身份的会话名称，用于区分不同的临时身份
            ->withRoleSessionName('external-username')
            //设置权限策略以进一步限制角色的权限
            //以下权限策略表示拥有所有OSS的只读权限
            ->withPolicy('{
             "Statement":[
                {
                     "Action":
                 [
                     "oss:Get*",
                     "oss:List*"
                     ],
                      "Effect": "Allow",
                      "Resource": "*"
                }
                   ],
          "Version": "1"
        }')
            ->connectTimeout(60)
            ->timeout(65)
            ->request();
        print_r($res->get('Credentials'));
        exit;
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
//        $douyinApp = DouyinApp::findOne(1);
//        $url = $douyinApp->buildConnectUrl('user_info','https://www.ttmei.vip/douyin/oauth');

        return $this->render('index',[]);
    }

    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        throw new \Exception(json_encode(Yii::$app->request->post()));
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }

        $model->password = '';
        return $this->render('login', [
            'model' => $model,
        ]);
    }
    public function actionLoginDouyin($code,$state)
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        throw new \Exception(json_encode(Yii::$app->request->post()));
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }

        $model->password = '';
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return Response|string
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        }
        return $this->render('contact', [
            'model' => $model,
        ]);
    }

    /**
     * Displays about page.
     *
     * @return string
     */
    public function actionAbout()
    {
        return $this->render('about');
    }
}
