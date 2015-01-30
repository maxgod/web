<?php

namespace MYB\MODELS;
use MYB\Base\Base;

class userModels extends Base
{
    public function __construct ()
    {
        $db_config = array(
            //      数据库名称  选择主从
            array('backstage','slave','mysql')
        );
        parent::__construct($db_config);
    }
    public function index()
    {
        $data = $this->backstage_slave->select('*')->where("")->from('qz_action')->SelectSingle();

    }

}
