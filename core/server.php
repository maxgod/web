<?php
$DIRECTORY = __DIR__;
include '..' . DIRECTORY_SEPARATOR .'lib.php';
$action = trim($_GET['action'],'/');
if(!$action)
    Response('没有此功能', 404);
$file_name = $DIRECTORY.DIRECTORY_SEPARATOR."server".DIRECTORY_SEPARATOR."ajax.".$action.".php";
if(!file_exists($file_name))
    Response('没有此功能', 404);
include_once $file_name;

