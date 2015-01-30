<?php 
$code = $_REQUEST['code'];
if(empty($code))
	exit;

include "base.php";
require_once PATH_ROOT."include/class/user/tqq.class.php";

$code = $_Global['request']['code'];
$openid = $_Global['request']['openid'];
$openkey = $_Global['request']['openkey'];
TqqOAuth::init('801439913','9f9134b8a9b76fb15848ec4bfb406b01');
$url = TqqOAuth::getAccessToken($code,$_Global['site_path']."tqq.php");
$result = TqqHttp::request($url);
parse_str($result,$args);
if($args['access_token'])
{
	$_Global['login_oauth']['tqq'] = array();
	$_Global['login_oauth']['tqq']['t_access_token'] = $args['access_token'];
	$_Global['login_oauth']['tqq']['t_expire_in'] = $args['expires_in'];
	$_Global['login_oauth']['tqq']['t_code'] = $code;
	$_Global['login_oauth']['tqq']['t_openid'] = $openid;
	$_Global['login_oauth']['tqq']['t_openkey'] = $openkey;
	//验证授权
	$result = TqqOAuth::checkOAuthValid();
	if(!$result)
	{
		exit('<h3>授权失败,请重试</h3>');
	}
}
else 
{
	exit($result);
}

$tqq = new TqqUser();
switch($callback_type)
{
	case 'login':
		$tqq->loginHandler();
		$url = FU('index/index');
	break;
	
	case 'bind':
		$tqq->bindHandler();
		$url = FU('settings/bind');
	break;
}

fSetCookie('callback_type','');
fHeader("location:".$url);
?>