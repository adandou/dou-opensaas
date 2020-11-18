<?php
namespace app\components;

use app\models\AuthAppRequest;
use Yii;
use AlibabaCloud\Client\AlibabaCloud;
use AlibabaCloud\Client\Exception\ClientException;
use AlibabaCloud\Client\Exception\ServerException;

class Func {
    //构造http multipart/form-data数据包
    static public function buildDouyinUploadData($content){
        $boundary = '--'.uniqid();
        $data = '--'.$boundary . "\r\n";
        $data .=
            'Content-Disposition: form-data; name="video"; filename="a.mp4"' . "\r\n"
            . 'Content-Type:video/mp4'."\r\n\r\n";
        $data .= $content. "\r\n";
        $data .= '--'.$boundary . "--\r\n";
        return [
            'boundary'=>$boundary,
            'data'=>$data,
            'len'=>strlen($data),
        ];

    }
    //传入日期返回日期所在周的所有天
    static public function getWeekDays($date){
        $days = [];
        $time = strtotime($date.'000000');
        $week_day = date('w',$time);
        //获取当前周几
        for ($i=1; $i<=7; $i++){
            $days[$i] = date('Ymd', strtotime( '+' . $i-$week_day .' days', $time));
        }
        return $days;
    }
    static public function formatFee($fee){
        return sprintf('%.2f',$fee/100);
    }
    /*
     * 获取当前url的host及path（http://xxx.xxx.xxx/xxx/xxx）
     */
    public static function getUrlHostPath()
    {
        return Yii::$app->request->getBaseUrl(1).'/'.Yii::$app->request->getPathInfo();
    }
    //带超时设置的file_get_contents
    public static function file_get_contents2($url, $timeout = 20)
    {
        $opts = array(
            'http'=>array(
                'method'=>"GET",
                'timeout'=>$timeout,
            ),
            'https'=>array(
                'method'=>"GET",
                'timeout'=>$timeout,
            )
        );
        $context = stream_context_create($opts);
        return file_get_contents($url, false, $context);
    }
    //带超时设置的file_get_contents
    public static function file_get_contents($url, $timeout = 20)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        $res = curl_exec($ch);
        curl_close($ch);
        $authAppRequest = new AuthAppRequest();
        $authAppRequest->request_url = $url;
        $authAppRequest->result = $res;
        $authAppRequest->save();
        return $res;
    }

    //post发送数据
    public static function postData($url, $data, $timeout=30, $headers = array())
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_POST, 1);
        if(is_array($data))
        {
            foreach ($data as $key => $val) {
                curl_setopt($ch, $key, $val);
            }
        }else{
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        }
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        if (!empty($headers)) {
            curl_setopt($ch,CURLOPT_HTTPHEADER, $headers);
        }
        $res = curl_exec($ch);
        curl_close($ch);

        $authAppRequest = new AuthAppRequest();
        $authAppRequest->request_url = $url;
        $authAppRequest->request_data = is_string($data)?$data:json_encode($data,JSON_UNESCAPED_UNICODE);
        $authAppRequest->result = $res;
        $authAppRequest->save();

        return $res;
    }
    //post发送上传文件，数据太大不记日志
    public static function postUploadData($url, $data, $timeout=30, $headers = array())
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_POST, 1);
        if(is_array($data))
        {
            foreach ($data as $key => $val) {
                curl_setopt($ch, $key, $val);
            }
        }else{
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        }
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        if (!empty($headers)) {
            curl_setopt($ch,CURLOPT_HTTPHEADER, $headers);
        }
        $res = curl_exec($ch);
        curl_close($ch);
        return $res;
    }

    public static function http_build_str($arr)
    {
        $arr2 = array();
        foreach ($arr as $key => $val) {
            $arr2[] = $key . '=' . $val;
        }
        return implode('&', $arr2);
    }

    /**
     * 通用sign生成
     */
    public static function sign($params, $secret = '')
    {
        ksort($params); //SORT_STRING | SORT_FLAG_CASE);
        $arr = array();
        foreach ($params as $key => $val) {
            $arr[] = "$key=$val";
        }

        return sha1(implode('&', $arr).$secret);
    }

    /**
     * 	作用：array转xml
     */
    public static function arrayToXml($arr)
    {
        $xml = "<xml>";
        foreach ($arr as $key=>$val) {
            if (is_numeric($val)) {
                $xml.="<".$key.">".$val."</".$key.">";

            }
            else
                $xml.="<".$key."><![CDATA[".$val."]]></".$key.">";
        }
        $xml.="</xml>";
        return $xml;
    }

    /**
     * 	作用：将xml转为array
     */
    public static function xmlToArray($xml)
    {
        //将XML转为array
        $array_data = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
        return $array_data;
    }

    //获取随机字符串
    public static function  getcode($length = 8)
    {
        $basestr = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ09871236873254";
        $str = "";
        while(strlen($str) < $length) {
            $str .= $basestr[rand(0,(strlen($basestr) - 1))];
        }
        return $str;
    }
    //获取随机数字
    public static function getRandNumber($length = 8)
    {
        $basestr = "09871236873254";
        $str = "";
        while(strlen($str) < $length) {
            $str .= $basestr[rand(0,(strlen($basestr) - 1))];
        }
        return $str;
    }
    //执行yii command
    public static function exec($route, $para=null, $sync = false){
        if(is_array($para)) $para = implode(' ', $para);
        if(!empty($para) && !is_string($para)) throw new \Exception(__CLASS__.':'.__METHOD__.":para error", 1);
        $cmd = Yii::$app->basePath . DIRECTORY_SEPARATOR . 'yii '.$route;
        if(!empty($para))$cmd.=' '.$para;
        shell_exec($cmd." > /dev/null ".($sync ? '' : '&'));
    }

    /**
     * Unicode编码转为汉字,字符,表情图标
     * @param string $unicode Unicode编码
     * @param string 转换后的汉字,字符或图标
     */
    public static function unicodeDecode($unicode) {
        return preg_replace_callback('/(\\\u[0-9a-f]{4})+/i', function($text) {
            if(!$text) return '';
            $text = $text[0];
            $decode = json_decode($text, true);
            if($decode) return $decode;
            $text = '["' . $text . '"]';
            $decode = json_decode($text);
            if(count($decode) == 1) return $decode[0];
            return $text;
        }, $unicode);
    }

    /**
     * 将内容进行UNICODE编码,编码后的内容格式:\u56fe\u7247 (原始:图片)
     * @param  [type] $name [description]
     * @return [type]       [description]
     */
    public static function unicodeEncode($name) {
        $name = iconv('UTF-8', 'UCS-2', $name);
        $len = strlen($name);
        $str = '';
        for ($i = 0; $i < $len - 1; $i = $i + 2) {
            $c = $name[$i];
            $c2 = $name[$i + 1];
            if (ord($c) > 0) {    // 两个字节的文字
                $str .= '\u' . base_convert(ord($c), 10, 16) . base_convert(ord($c2), 10, 16);
            } else {
                $str .= $c2;
            }
        }
        return $str;
    }


    public static function file_get_contents3($url, $params) {
        $aContext = array(
            'http' => array('method' => 'GET',
                            'header'  => 'Content-Type: application/json',
                            'content' => $params )
        );
        $cxContext  = stream_context_create($aContext);
        return  @file_get_contents($url,false,$cxContext);

    }
    /**
    * 计算两组经纬度间的距离
    * params ：lat1 纬度1； lng1 经度1； lat2 纬度2； lng2 经度2； len_type （1:m or 2:km);
    * return m or km
    */
    public static function getDistance($lat1, $lng1, $lat2, $lng2, $len_type = 1, $decimal = 2) {
        $pi = pi();
        $earthRadiu = 6378.137; //地球半径
        $radLat1 = $lat1 * $pi / 180.0;
        $radLat2 = $lat2 * $pi / 180.0;
        $a = $radLat1 - $radLat2;
        $b = ($lng1 * $pi / 180.0) - ($lng2 * $pi / 180.0);
        $s = 2 * asin(sqrt(pow(sin($a/2),2) + cos($radLat1) * cos($radLat2) * pow(sin($b/2),2)));
        $s = $s * $earthRadiu;
        $s = round($s * 1000);
        if ($len_type > 1) {
            $s /= 1000;
        }
        return round($s, $decimal);
    }

    /**
     * 生成唯一 id
     * @return string
     */
    public static function createGuid()
    {
        $charId = strtoupper(md5(uniqid(mt_rand(), true)));
        $uuid   = substr($charId, 0, 8)
            . substr($charId, 8, 4)
            . substr($charId, 12, 4)
            . substr($charId, 16, 4)
            . substr($charId, 20, 12);
        return $uuid;
    }
    //分词,根据长度列出所有排列
    public static function splitWordsByLen($str, $len=2){
        //排除手机号
        $str = preg_replace("/[0-9]{11}/",'',$str);
        $str = preg_replace('/[\s]+/','',$str);
        $str = preg_replace('/[\p{P}]+/','',$str);
        $arr_all = [];
        for($i=0;$i<$len;$i++){
            preg_match_all("/.{".$len."}/u", $str, $arr);
            $arr_all = array_merge($arr_all,$arr[0]);
            $str = mb_substr($str,1);
        }
        return $arr_all;
    }
    //验证手机号
    public static function checkPhone($phone){
        $check = '/^(1(([35789][0-9])|(47)))\d{8}$/';
        if (preg_match($check, $phone)) return true;
        else return false;
    }

}