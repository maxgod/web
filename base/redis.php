<?php

namespace MYB\Base;

/**
 *
 * @author pan
 */
class Redis {

    /**
     *
     * @var 服务
     */
    public $server;

    /**
     *
     * @var array 以建立的链接
     */
    static $connected = array();

    /**
     *
     * @param array $config 初始化数据库链接参数
     */
    public function __construct (array $config)
    {
        $key = json_encode($config);
        if (isset(self::$connected[$key]))
        {
            $this->pdo = self::$connected[$key];
        }
        else
        {
            $redis = new \Redis();
            $redis->connect($config['host'], $config['port']);
            if (! empty($config['pass']))
            {
                $redis->auth($config['pass']);
            }
            self::$connected[$key] = $redis;
             $this->server = self::$connected[$key];
        }
    }

    /**
     * 销毁当前链接
     */
    public function Destroy ()
    {
        unset($this->server);
    }
}

?>