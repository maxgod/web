<?php

/**
 * 自动加载类定义文件
 *
 * @param string $class_name 类名
 */
function __autoload ($class_name)
{
    global $DIRECTORY;
    $suffix = ".php";

    $fregment = explode('\\', $class_name);
    $count = count($fregment);
    $class_name_short = $fregment[$count - 1];
    unset($fregment[$count - 1]);
    $path = $DIRECTORY . DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, $fregment) . DIRECTORY_SEPARATOR;
    if (strstr($path, 'MYB'))
    {
        $folder_name = substr($DIRECTORY, strrpos($DIRECTORY, DIRECTORY_SEPARATOR));
        $path = str_replace($folder_name . DIRECTORY_SEPARATOR . 'MYB' . DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR, $path);
    }
    $chars = array();
    foreach (str_split($class_name_short) as $char)
    {
        $ascii = ord($char);
        if ($ascii >= 65 && $ascii <= 90)
        {
            $chars[] = '.';
            $char = strtolower($char);
        }
        $chars[] = $char;
    }
    if ($chars[0] == '.')
    {
        unset($chars[0]);
    }
    $chars = implode('', $chars);
    include strtolower($path) . $chars. $suffix;
}


/*
 * 加载文件
 *
 */
function file_load($path,$file_name)
{
    if(!$path)
        return false;
    if(!$file_name)
        return false;
    return  "{$path}{$file_name}.php";
}

/**
 * 响应客户端请求
 *
 * @param string $message 消息
 * @param number $custom_code 自定义状态编号
 * @param string|array $data 数据
 * @param number $status_code HTTP状态码
 */
function Response ($message, $custom_code = 0, $data = NULL, $status_code = 200)
{
    global $DIRECTORY;
    $result = array(
            'status_code' => $custom_code,
            'message' => $message,
            'data' => $data
    );
    switch ($status_code)
    {
        case 201:
            $status_code = '201 Created';
            break;
        case 202:
            $status_code = '202 Accepted';
            break;
        case 204:
            $status_code = '204 No Content';
            break;
        case 303:
            $status_code = '303 See Other';
            break;
        case 404:
            include_once $DIRECTORY.DIRECTORY_SEPARATOR."template".DIRECTORY_SEPARATOR.'404.htm';
            exit();
            break;
        default:
            break;
    }
    
    header($_SERVER['SERVER_PROTOCOL'] . ' ' . $status_code);
    header('Content-Type: application/json');
    echo json_encode($result);
    exit();
}

/**
 * 根据参数名获取请求参数的值
 *
 * @param string $key 参数名
 * @param string $msg 提示信息
 * @param bool $necessary 是否必须
 * @param bool $allow_empty 是否能为空值
 * @return string
 */
function GetRequestValue ($key,$necessary = true, $allow_empty = false, $msg="" )
{
    if (! isset($_REQUEST[$key]))
    {
        if ($necessary)
        {
            $msg = $msg?$msg:"丢失参数：$key";
            Response($msg, 400);
        }
        return null;
    }
    //数组无法用trim验证
    if(is_array($_REQUEST[$key]))
        $result = $_REQUEST[$key];
    else
        $result = trim($_REQUEST[$key]);
    if (empty($result))
    {
        if ($necessary && ! $allow_empty)
        {
            $msg = $msg?$msg:"参数不能为空：$key";
            Response($msg, 400);
        }
    }
    return trim($_REQUEST[$key]);
}
/*
 *  创建COOKIE
 *
 * @param string $name COOKIE名称
 * @param string $value COOKIE值
 * @param int $life 时长
 * @param int $http_only
 * @return string
 *
 */
function cookiet($name,$value,$life, $http_only = false)
{
    global $_Global;

    $cookie_domain = ''; 		// COOKIE作用域
    $cookie_path   = '/'; 		// COOKIE作用路径
    $_Global['cookie'][$name] = $value;
    $_Global['cookie'][$name];
    $_COOKIE[$name] = $value;
    $life = $life > 0 ? time() + $life : ($life < 0 ? -1 : 0);
    $path = $http_only && PHP_VERSION < '5.2.0' ? $cookie_path.'; HttpOnly' : $cookie_path;
    $secure = $_SERVER['SERVER_PORT'] == 443 ? 1 : 0;
    if(PHP_VERSION < '5.2.0')
    {
        setcookie($name, $value, $life, $path, $cookie_domain, $secure);
    }
    else
    {
        setcookie($name, $value, $life, $path, $cookie_domain, $secure, $http_only);
    }
}
//抛出错误
function onError() {
    global $DIRECTORY;
    $last_error = error_get_last();
    if($last_error['type'] == E_PARSE || $last_error['type'] == E_ERROR || $last_error['type'] === E_USER_ERROR)
    {
        //错误500页面
        $file_name = $DIRECTORY.DIRECTORY_SEPARATOR."template".DIRECTORY_SEPARATOR."500.htm";
        $msg = "致命错误：".$last_error['message']."<br/>在文件：".$last_error['file']."    第".$last_error['line']."行";
        if(file_exists($file_name))
            include_once $file_name;
    }
}
//多语言处理
function L($file_name = "",$language = "")
{
    $file_path = PATH_LANGUAGE.$language;
    if(!$language)
    {
        $file_path .= LANGUAGE;
    }
    if(!$file_name)
    {
        $file_path .= DIRECTORY_SEPARATOR."view".DIRECTORY_SEPARATOR.MODELS_NAME."_".ACTION_NAME.".lang.php";
    }
    else
    {
        $file_path .= DIRECTORY_SEPARATOR."view".DIRECTORY_SEPARATOR.$file_name.".lang.php";;
    }
    if(file_exists($file_path))
    {
        return include_once $file_path;
    }
    return false;
}
/**
 * 跳转至指定地址
 *
 * @param string $url 跳转地址
 */
function Redirect ($url)
{
    header("HTTP/1.1 302 Found");
    Header("Location:$url");
    exit();
}

// 自定义boolean类型转换函数
if (! function_exists('BoolVal'))
{

    function BoolVal ($var)
    {
        return (bool) $var;
    }
}

/**
 * 分页处理
 * @param string $type 所在页面 topic/index
 * @param array  $args 参数 array('')
 * @param int $total_count 总数
 * @param int $page 当前页
 * @param int $page_size 分页大小
 * @param string $url 自定义路径
 * @param int $offset 偏移量
 * @return array
 */
function buildPage($type,$args,$total_count,$page = 1,$page_size = 20,$url='',$offset = 5)
{
    $pager['total_count'] = intval($total_count);
    $pager['page'] = $page;
    $pager['page_size'] = $page_size;
    /* page 总数 */
    $pager['page_count'] = ($pager['total_count'] > 0) ? ceil($pager['total_count'] / $pager['page_size']) : 1;

    /* 边界处理 */
    if ($pager['page'] > $pager['page_count'])
        $pager['page'] = $pager['page_count'];

    $pager['limit'] = ($pager['page'] - 1) * $pager['page_size'] . "," . $pager['page_size'];

    $page_prev  = ($pager['page'] > 1) ? $pager['page'] - 1 : 1;
    $page_next  = ($pager['page'] < $pager['page_count']) ? $pager['page'] + 1 : $pager['page_count'];
    $pager['prev_page'] = $page_prev;
    $pager['next_page'] = $page_next;

    if (!empty($url))
    {
        $pager['page_first'] = $url . 1;
        $pager['page_prev']  = $url . $page_prev;
        $pager['page_next']  = $url . $page_next;
        $pager['page_last']  = $url . $pager['page_count'];
    }
    else
    {
        $args['page'] = '_page_';
        if(!empty($type))
            $page_url = FU($type,$args);
        else
            $page_url = 'javascript:;';

        $pager['page_first'] = str_replace('_page_',1,$page_url);
        $pager['page_prev']  = str_replace('_page_',$page_prev,$page_url);
        $pager['page_next']  = str_replace('_page_',$page_next,$page_url);
        $pager['page_last']  = str_replace('_page_',$pager['page_count'],$page_url);
    }

    $pager['page_nums'] = array();

    if($pager['page_count'] <= $offset * 2)
    {
        for ($i=1; $i <= $pager['page_count']; $i++)
        {
            $pager['page_nums'][] = array('name' => $i,'url' => empty($url) ? str_replace('_page_',$i,$page_url) : $url . $i);
        }
    }
    else
    {
        if($pager['page'] - $offset < 2)
        {
            $temp = $offset * 2;

            for ($i=1; $i<=$temp; $i++)
            {
                $pager['page_nums'][] = array('name' => $i,'url' => empty($url) ? str_replace('_page_',$i,$page_url) : $url . $i);
            }

            $pager['page_nums'][] = array('name'=>'...');
            $pager['page_nums'][] = array('name' => $pager['page_count'],'url' => empty($url) ? str_replace('_page_',$pager['page_count'],$page_url) : $url . $pager['page_count']);
        }
        else
        {
            $pager['page_nums'][] = array('name' => 1,'url' => empty($url) ? str_replace('_page_',1,$page_url) : $url . 1);
            $pager['page_nums'][] = array('name'=>'...');
            $start = $pager['page'] - $offset + 1;
            $end = $pager['page'] + $offset - 1;

            if($pager['page_count'] - $end > 1)
            {
                for ($i=$start;$i<=$end;$i++)
                {
                    $pager['page_nums'][] = array('name' => $i,'url' => empty($url) ? str_replace('_page_',$i,$page_url) : $url . $i);
                }

                $pager['page_nums'][] = array('name'=>'...');
                $pager['page_nums'][] = array('name' => $pager['page_count'],'url' => empty($url) ? str_replace('_page_',$pager['page_count'],$page_url) : $url . $pager['page_count']);
            }
            else
            {
                $start = $pager['page_count'] - $offset * 2 + 1;
                $end = $pager['page_count'];
                for ($i=$start;$i<=$end;$i++)
                {
                    $pager['page_nums'][] = array('name' => $i,'url' => empty($url) ? str_replace('_page_',$i,$page_url) : $url . $i);
                }
            }
        }
    }

    return $pager;
}
/*
 * 地址组装
 *
 */
function FU($url,$args)
{
    $urls = explode("/",$url);
    $url = "index.php";
    $str = "";
    if(isset($urls[1]))
    {
        $str = "?a=".$urls[1]."&m=".$urls[0];
    }
    $args = array_filter($args);
    foreach ($args as $key=>$val)
    {
        if($str == "")
        {
            $str .= "?";
        }
        else
        {
            $str .= "&";
        }
        $str .= $key."=".$val;
    }

    $url = $url.$str;
    return $url;
}