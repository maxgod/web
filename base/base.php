<?php

namespace MYB\Base;
use MYB\Base\Mysql;
use MYB\Base\Redis;
use MYB\Base\Mongo;
use MYB\Base\Memcache;

/**
 *
 * @author 提供数据库链接
 */
class Base {

    /**
     *
     * @var MySQLDA mysql链接
     */
    protected $mysql;

    /**
     *
     * @var redis server
     */
    protected $redis;

    /**
     *
     * @var mongo server
     */
    protected $mongo;

    /**
     *
     * @var mongo server
     */
    protected $memcache;


    /**
     * 初始化数据库链接参数
     */
    public function __construct (array $db_config)
    {
        foreach ($db_config as $db => $config)
        {
            switch ($config[2])
            {
                case 'mysql':
                    $server_name = $config[0];
                    $server_id = $config[1];
                    $link = $server_name."_".$server_id;
                    $MYSQL = $GLOBALS['_DATABASE']['MYSQL'][$server_name][$server_id];
                    if($server_id == "slave")
                    {
                        $rand = rand(0,count($MYSQL)-1);
                        $MYSQL = $MYSQL[$rand];
                    }
                    $this->$link = new Mysql($MYSQL);
                    break;
                case 'redis':
                    $server_name = $config[0];
                    $server_id = $config[1];
                    $link = $server_name."_".$server_id;
                    $this->$link = new Redis($GLOBALS['_DATABASE']['REDIS'][$server_name][$server_id]);
                    break;
                case 'mongo':
                    $server_name = $config[0];
                    $server_id = $config[1];
                    $link = $server_name."_".$server_id;
                    $this->$link =  new Mongo($GLOBALS['_DATABASE']['MONGO'][$server_name][$server_id]);
                    break;
                case 'memcache':
                    $server_name = $config[0];
                    $server_id = $config[1];
                    $link = $server_name."_".$server_id;
                    $this->$link =  new Memcache($GLOBALS['_DATABASE']['MEMCACHE'][$server_name][$server_id]);
                    break;
                default:
                    break;

            }
        }
    }
}

?>