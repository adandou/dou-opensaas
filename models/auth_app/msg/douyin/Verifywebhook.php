<?php
namespace app\models\auth_app\msg\douyin;

use Yii;
use app\components\Func;

/**
 * webhook验证".
 */
class Verifywebhook extends \app\models\AuthAppMsg
{
    public function run(){
        $msg_arr = json_decode($this->content, 1);
        return json_encode($msg_arr['content']);
    }
}
