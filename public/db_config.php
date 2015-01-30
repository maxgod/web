<?php

/**
 * mysql服务器及帐号信息
 */
$_DATABASE['MYSQL'] = array(
        'backstage' => array(
                'master' => array(
                        'host' => '192.168.10.196',
                        'port' => 33066,
                        'db' => 'backstage',
                        'user' => 'root',
                        'pass' => '3eW2c85D2e'
                ),
                'slave' => array(
                    array(
                        'host' => '192.168.10.196',
                        'port' => 33066,
                        'db' => 'backstage',
                        'user' => 'root',
                        'pass' => '3eW2c85D2e'
                    ),
                    array(
                        'host' => '192.168.10.196',
                        'port' => 33066,
                        'db' => 'backstage',
                        'user' => 'root',
                        'pass' => '3eW2c85D2e'
                    )
                )
        )
);

?>