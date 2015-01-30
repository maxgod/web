<?php
namespace SERVICE;

class ViewService
{
    public $lang;
    public function __construct ($route_info)
    {
        //定义全局数据库链接
        global $_DATABASE,$_GLOBALS;
        //使用的语言模板
        define('LANGUAGE','jane');
        define('PATH',dirname(dirname(__DIR__)));
        define('ACTION_NAME',$route_info['action']);
        define('MODELS_NAME',$route_info['models']);
        define('PATH_TEMPLATE',dirname(__DIR__).DIRECTORY_SEPARATOR."template".DIRECTORY_SEPARATOR);
        define('VERSION','v1');
        define('IS_LANGUAGE',FALSE);
        define('PATH_LANGUAGE',PATH.DIRECTORY_SEPARATOR."language".DIRECTORY_SEPARATOR);
        define('PATH_PUBLIC',PATH.DIRECTORY_SEPARATOR."public".DIRECTORY_SEPARATOR);
        include file_load(PATH_PUBLIC,'db_config');
        if(IS_LANGUAGE)
            $this->lang = L();
        $_GLOBALS = $GLOBALS['_COOKIE'];
    }
    //引用前端模板
    public function display($file_name = "",$is_cache = false)
    {
        if(empty($file_name))
        {
            $file_name = strtolower(PATH_TEMPLATE .VERSION.DIRECTORY_SEPARATOR."html".DIRECTORY_SEPARATOR.ACTION_NAME.".htm");;
        }
        if(!$is_cache)
        {
            include_once $file_name;
        }
        else
        {
            //创建缓存文件
        }
    }
    //加载外部文件
    public function load($class_name,$path = "util/",$is_auto_obj = true)
    {
        $file_name = '..' . DIRECTORY_SEPARATOR .$path.$class_name;
        if(file_exists($file_name))
        {
            include_once '..' . DIRECTORY_SEPARATOR .$path.$class_name;
            $this->$class_name = new $class_name;
        }
        else
        {
            include_once PATH_TEMPLATE.'404.htm';
        }
    }
}