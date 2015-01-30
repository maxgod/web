<?php
$site_path = "http://www.pinyouc.com/";
define('ROOT_PATH', str_replace('callback/base.php', '', str_replace('\\', '/', __FILE__)));
define('SUB_DIR','/callback');
define('MODULE_NAME','callback');
$callback = array(
    'qq'=>array(
       'appid'=>'101175416',
       'appkey'=>'ee91ca81f911bde4c6e281bfd2214d9c'
    ),
    'sina'=>array(
        'appid'=>'1764304977',
        'appkey'=>'42872a2c3db34deea9021caa817277a5'
    )

);

session_start();
$callback_type = "login";//$_SESSION['callback_type'];
if(empty($callback_type))
	exit;

//获取url的内容
function getCurl($url,$data)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL,$url);
    curl_setopt($ch, CURLOPT_TIMEOUT,30);
    curl_setopt($ch,CURLOPT_POST,true);
    curl_setopt($ch,CURLOPT_POSTFIELDS,$data);
    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/535.19 (KHTML, like Gecko) Chrome/18.0.1025.168 Safari/535.19");
    curl_setopt($ch, CURLOPT_REFERER,"http://www.pinyouc.com/");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $content = curl_exec($ch);
    if(curl_errno($ch))
    {
        return -1;
    }
    else
    {
        $httpStatusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if (200 !== $httpStatusCode)
        {
            return -1;
        }
    }
    curl_close($ch);
    return $content;
}


?>