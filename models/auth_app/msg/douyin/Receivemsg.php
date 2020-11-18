<?php
namespace app\models\auth_app\msg\douyin;

use Yii;
use app\components\Func;

/**
 * webhook私信".
 */
class Receivemsg extends \app\models\AuthAppMsg
{
    public function run(){
        $msg_arr = json_decode($this->content, 1);
        $content_arr = json_decode($msg_arr['content'], 1);
        switch($content_arr['content']['message_type']){
            case 'text':
                break;
            default:
                break;
        }
        print_r($content_arr);
        return json_encode($msg_arr['content']);
    }
}
