<?php 
$code = $_REQUEST['code'];
if(empty($code))
	exit;

require_once "base.php";
require_once "user/sina.class.php";
require_once "sdks/sina/saetv2.ex.class.php";
$appid = $callback['sina']['appid'];
$appkey = $callback['sina']['appkey'];
$oauth= new SaeTOAuthV2($appid,$appkey);
$keys['code'] = $_REQUEST['code'];
$keys['redirect_uri'] = $site_path."callback/sina.php";
try
{
	$token = $oauth->getAccessToken('code',$keys);
}
catch (OAuthException $e)
{
	die($e->getMessage());
}

$sina = new SinaUser($appid,$appkey);
switch($callback_type)
{
	case 'login':
		$sina->loginHandler($token);
		//$url = FU('index/index');
	break;
	
	case 'bind':
		$sina->bindHandler($token);
		//$url = FU('settings/bind');
	break;
}
$_SESSION['callback_type'] = "";
//cookiet('callback_type','');
//header("location: ".$url);
?>