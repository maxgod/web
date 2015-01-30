<?php
require_once "base.php";
require_once "user/qq.class.php";
require_once "sdks/qq/qq.func.php";
$appid = $callback['qq']['appid'];
$appkey = $callback['qq']['appkey'];
$access_token = getQqAccessToken($appid,$appkey,$site_path);
$openid = getQqOpenid($access_token);
$qq = new QqUser($appid,$appkey);
switch($callback_type)
{
    case 'login':
        $qq->loginHandler($access_token,$openid);
    break;
    case 'bind':
        $qq->bindHandler($access_token,$openid);
    break;
}

$_SESSION['callback_type'] = "";
//header("location:".$url);
?>