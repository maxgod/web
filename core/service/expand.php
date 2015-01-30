<?php
/*
*自定义关键字引用功能
*/
define('IS_STATIC',true);
define('SITE_PATH',"http://127.0.0.1/");

function PAGE($route,$args)
{
    $str = "";
    foreach($args as $key=>$val)
    {
        $param = $val;
        if(substr_count($val,"\$"))
            $param = "<?php echo {$val}; ?>";
        $str .= $str?"&":"";
        $str .= "{$key}={$param}";
    }
    if(!IS_STATIC)
    {
        $routes = explode('/',$route);
        $url = "index.php?models=".$routes[0]."&action=".$routes[1]."&".$str;
    }
    else
    {
        $url = "{$route}?{$str}";
    }
    return $url;
}
/*
 * 读取图片
 *{img $img width=123px,}
 */
function images($url,$param,$width = 0,$height = 0)
{
    $default_src = "./upload/noavatar/image.gif";
    $html = "<img src='{$default_src}' #param# original='#img#' >";
    if(file_exists($url))
    {
        $src= "默认图";
        $html = str_replace(array('#img#','#param#'),array($src,$param),$html);
        return $html;
    }
    else
    {
        $img_param['src'] = $url;
        $img_param['width'] = $width;
        $img_param['height'] = $height;
        $src= SITE_PATH."getimg.php?args=".base64_encode(json_encode($img_param));
        $html = str_replace(array('#img#','#param#'),array($src,$param),$html);
        return $html;
    }
}
/*
 *
 *
 */
