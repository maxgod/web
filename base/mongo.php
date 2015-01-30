<?php

namespace MYB\Base;

/**
 *
 * @author pan
 */
class Mongo {

    /**
     *
     * @var mongo服务
     */
    public $server;

    /**
     *
     * @var array 以建立的数据库链接
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
            $this->server = self::$connected[$key];
        }
        else
        {
            $username = $config['user'];
            $password = $config['pass'];
            $host = $config['host'];
            $port = $config['port'];
            $dbname = $config['dbname'];
            $host = ($port) ? ($host . ':' . $port) : $host;
            $connection_string = "mongodb://{$username}:{$password}@{$host}/{$dbname}";
            self::$connected[$key] = $this->server = new \Mongo($connection_string,
                    array(
                            'connect' => true,
                            'connectTimeoutMS' => $config['timeout'],
                            'wTimeout' => $config['timeout']
                    ));
            $this->server = self::$connected[$key];
        }
    }

    /**
     *
     * @param string $db
     */
    public function SelectDB ($db)
    {
        return $this->server->selectDB($db);
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