<?php
class SinaUser
{
	public $config;
	private $type = 'sina';

    private $app_key = "";
    private $app_secret = "";
	public function SinaUser($app_key,$app_secret)
	{
        $this->app_key = $app_key;
        $this->app_secret = $app_secret;
	}
	
	public function loginHandler($token)
	{
		$user = $this->getUserInfo($token);
        $user['user_name'] = $user['screen_name'];
        $user['id'] = $user['id'];
        $user['profile_url'] = $user['profile_url'];
        $location = explode(" ",$user['location']);
        $user['province'] = $location[0];
        $user['city'] = $location[1];
        $user['img_url'] = str_replace('/50/','/180/',$user['profile_image_url']);
        $user['access_token'] = $user['token']['access_token'];
        $data['type'] = $this->type;
        $data['openid'] = $user['id'];
        $data['user'] = json_encode($user);
        $bind_user = getCurl("http://127.0.0.1/server.php?a=bind_login",$data);
        $bind_user = json_decode($bind_user);
        if($bind_user->status)
        {
            $time = time()+31536000;
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
	
	public function bindHandler($token)
	{
		$user = $this->getUserInfo($token);
        $user['user_name'] = $user['screen_name'];
        $user['id'] = $user['id'];
        $user['profile_url'] = $user['profile_url'];
        $location = explode(" ",$user['location']);
        $user['province'] = $location[0];
        $user['city'] = $location[1];
        $user['img_url'] = str_replace('/50/','/180/',$user['profile_image_url']);
        $user['access_token'] = $user['token']['access_token'];
        $data['type'] = $this->type;
        $data['openid'] = $user['id'];
        $data['user'] = json_encode($user);
        $bind_user = getCurl("http://127.0.0.1/server.php?a=bind_login",$data);
        $bind_user = json_decode($bind_user);
        if($bind_user->status)
        {
            $time = time()+31536000;
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


	public function getUserInfo($token)
	{
		global $_Global;
		$client = new SaeTClientV2($this->config['app_key'],$this->config['app_secret'],$token['access_token']);
		$result = $client->show_user_by_id($token['uid']);
		
		if ($result === false || $result === null)
			exit("Error occured");
		
		if (isset($result['error_code']) && isset($result['error']))
			exit('Error_code: '.$result['error_code'].';  Error: '.$result['error']);
		
		$result['token'] = $token;
		return $result;
	}
	
	
}
?>