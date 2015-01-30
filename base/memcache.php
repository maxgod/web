<?php

namespace MYB\Base;

/**
 *
 * @author pan
 */
class Memcache
{

    /**
     *
     * @var array 以建立的数据库链接
     */
    static $connected = array();
    /**
     *
     * @var 服务
     */
    public $server;


    /**
     * 构造函数
     * @param array $config 配置信息
     */
    function __construct(array $config)
    {
        $key = json_encode($config);
        if (isset(self::$connected[$key])) {
            $this->server = self::$connected[$key];
        } else {
            $server = new \Memcache();
            $server->pconnect($config['host'], $config['port']);
            self::$connected[$key] = $server;
            $this->server = self::$connected[$key];
        }
    }
}

?>