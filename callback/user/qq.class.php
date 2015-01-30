<?php
class QqUser
{
	public  $config;
	private $type = 'qq';
	private $app_key = "";
    private $app_secret = "";
	public function QqUser($app_key,$app_secret)
	{
        $this->app_key = $app_key;
        $this->app_secret = $app_secret;
	}
	
	public function loginHandler($access_token,$openid)
	{
		$user = $this->getUserInfo($access_token,$openid);
        $data['type'] = $this->type;
        $data['openid'] = $openid;
        $data['user'] = json_encode($user);
        $bind_user = getCurl("http://127.0.0.1/server.php?a=bind_login",$data);
        $bind_user = json_decode($bind_user);

        if($bind_user->status)
        {
            $time = time()+31536000;
            //可以自己配置
            setcookie('id',$bind_user->user->user_id,$time,'/');
            setcookie('user_name',$bind_user->user->user_name,$time,'/');
            setcookie('img_url',$bind_user->user->img_url,$time,'/');
            setcookie('email',$bind_user->user->email,$time,'/');
            setcookie('recommend',$bind_user->user->recommend,$time,'/');
            setcookie('code',$bind_user->user->code,$time,'/');
            header("HTTP/1.1 302 Found");
            header("location: /".$bind_user->url);
            exit();
        }
        else
        {
            header("HTTP/1.1 302 Found");
            header("location: http://www.pinyouc.com/");
            exit();
        }
	}
	
	public function bindHandler($access_token,$openid)
	{
		$user = $this->getUserInfo($access_token,$openid);
        $data['type'] = $this->type;
        $data['openid'] = $openid;
        $data['user'] = json_encode($user);
        $bind_user = getCurl("http://127.0.0.1/server.php?a=bind_login",$data);
        $bind_user = json_decode($bind_user);
        if($bind_user->status)
        {
            $time = time()+31536000;
            //可以自己配置
            setcookie('id',$bind_user->user->user_id,$time,'/');
            setcookie('user_name',$bind_user->user->user_name,$time,'/');
            setcookie('img_url',$bind_user->user->img_url,$time,'/');
            setcookie('email',$bind_user->user->email,$time,'/');
            setcookie('recommend',$bind_user->user->recommend,$time,'/');
            setcookie('code',$bind_user->user->code,$time,'/');
            header("HTTP/1.1 302 Found");
            header("location: /".$bind_user->url);
            exit();
        }
        else
        {
            header("HTTP/1.1 302 Found");
            header("location: http://www.pinyouc.com/");
            exit();
        }
	}

	
	public function getUserInfo($access_token,$openid)
	{
		$user = getQqUserInfo($this->app_key,$access_token,$openid);
		if(!isset($user['nickname']))
			exit;
		$user['access_token'] = $access_token;
		$user['openid'] = $openid;
		return $user;
	}
}
?>