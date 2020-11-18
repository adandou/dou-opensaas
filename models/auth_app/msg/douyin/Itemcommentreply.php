<?php
namespace app\models\auth_app\msg\douyin;

use Yii;
use app\components\Func;

/**
 * webhook评论回复".
 */
class Itemcommentreply extends \app\models\AuthAppMsg
{
    public function run(){
        $msg_arr = json_decode($this->content, 1);
        $content_arr = json_decode($msg_arr['content'], 1);
        print_r($content_arr);
        return json_encode($msg_arr['content']);
    }
}
