<?php
//error_reporting(0);
$DIRECTORY = __DIR__;
include '..' . DIRECTORY_SEPARATOR .'lib.php';
register_shutdown_function("onError");
$action = isset($_GET['action'])?trim($_GET['action'],'/'):"";
$models = isset($_GET['models'])?trim($_GET['models'],'/'):"";
$route_info['models'] = strtolower(empty($models) ? 'index' : $models);
$route_info['action'] = strtolower(empty($action) ?  'index'  : $action);
// 根据路由信息，执行控制器相应操作
try
{
    $class_name = "view\\{$route_info['models']}View";
    $class = new \ReflectionClass($class_name);
}
catch (\Exception $e)
{
    Response('对不起没有这个页面', 404,NULL,404);
}
$controller = $class->newInstance($route_info);
if (! method_exists($controller, $route_info['action']))
{
    Response('对不起没有这个页面', 404,NULL,404);
}
$controller->$route_info['action']();
