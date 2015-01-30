<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 14-11-7
 * Time: 上午11:18
 */
namespace MYB\Base;
class Mysql
{

    /**
     * @var array 已建立的PDO链接集合
     */
    static $connected = array();
    /*
     * @var PDOStatement PDO操作对象
     *
     */
    protected  $pdo_statement;

    /*
     * @var string 查询条件
     *
     */
    protected $where = '';


    /*
     * @var string 链接名
     *
     */
    protected $join = '';

    /*
     * @var string 表名
     *
     */
    protected $table = '';


    /*
     * @var string 查询字段
     *
     */
    protected $field = '*';

    /*
     * @var array 分页
     *
     */
    protected $limit = "";
    /*
     * @var string 数据库语句
     *
     */
    protected $sql = "";

    /*
     * @var string 排序条件
     *
     */
    protected $order = "";

    /*
     * @var string
     *
     */
    protected $group = "";

    /**
     * @var \PDO PDO链接
     */
    public $pdo;
    /**
     * @param array $config MYSQL链接配置参数
     */
    public function __construct(array $config)
    {
        $key = json_encode($config);
        if (isset(self::$connected[$key])) {
            $this->pdo = self::$connected[$key];
        } else {
            $connection_string = "mysql:host={$config['host']};port={$config['port']};dbname={$config['db']};charset=utf8mb4";
            $user = $config['user'];
            $password = $config['pass'];
            $option = array(
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION
            );
            self::$connected[$key] = new \PDO($connection_string, $user, $password, $option);
            $this->pdo = self::$connected[$key];
        }

    }

    /**
     * 销毁当前链接
     */
    public function Destroy()
    {
        unset($this->pdo);
    }
    /*
     *
     * 编辑获取字段
     * @return pdo
     *
     */
    public  function select($field)
    {
        if($field)
            $this->field = $field;
        return $this;
    }
    /*
     *
     * 表名
     * @return pdo
     *
     */
    public  function from($table)
    {
        if($table)
            $this->table = $table;
        return $this;
    }
    /*
     *
     * 链接表名
     * @param string $join
     * @param string $type
     * @return pdo
     *
     */
    public  function join($join, $type)
    {
        if($join)
            $this->join .= "{$type} join {$join}";
        return $this;
    }


    /*
     *
     * 获取记录分页
     * @param int $start_page
     * @param int $end_page
     * @return pdo
     *
     */
    public function limit($start_page,$end_page)
    {
        if($start_page && $end_page){$this->limit = " limit {$start_page},{$end_page}";}
        elseif($end_page){$this->limit = " limit {$end_page}";}
        return $this;
    }


    /*
     *
     * 加入查询条件
     * @param string $where
     * @return pdo
     *
     */
    public function where($where)
    {
        if($where)
            $this->where = " where {$where}";
        return $this;
    }
    /*
     *
     * 开启事物
     * @param string $where
     * @return pdo
     *
     */
    public function beginTransaction()
    {
        $this->pdo->setAttribute(\PDO::ATTR_AUTOCOMMIT, 0);
        $this->pdo->beginTransaction();
    }
    /*
     *
     * 结束事物
     * @param string $where
     * @return pdo
     *
     */
    public function rollBack()
    {
        $this->pdo->rollBack();
        $this->pdo->setAttribute(\PDO::ATTR_AUTOCOMMIT, 1);
    }
    /*
     *
     * 执行事物
     * @param string $where
     * @return pdo
     *
     */
    public function commit()
    {
        $this->pdo->commit();
        $this->pdo->setAttribute(\PDO::ATTR_AUTOCOMMIT, 1);
    }
    /*
     *
     * 排序
     * @param string $order
     * @return pdo
     *
     */
    public function order($order)
    {
        if($order)
            $this->order = " order by {$order}";
        return $this;
    }
    /*
     *
     * 归类
     * @param string $order
     * @return pdo
     *
     */
    public function group($group)
    {
        if($group)
            $this->group = " group by {$group}";
        return $this;
    }
    /*
     * 组装SQL语句
     * @param string $operation  (状态 insert 插入  update 编辑  select 查询   delete 删除)
     *
     */
    protected function CombinationSql($operation,array &$data = array())
    {
        if(!empty($data))
        {
            $set_parts = "";
            foreach($data as $key=>$item)
            {
                if(is_numeric($item) || preg_match("/^({$key}\+)[0-9]+$/", $item))
                    $set_parts .= ", `{$key}` = {$item}";
                else
                    $set_parts .= ", `{$key}` = '{$item}'";
            }
            $set_parts = substr($set_parts, 1);
        }
        switch($operation)
        {
            case 'insert':
                $sql = "insert into {$this->table} {$set_parts}";
                break;
            case 'update':
                $sql = "update {$this->table} {$set_parts} {$this->where}";
                break;
            case 'select':
                $sql = "select {$this->field} from {$this->table} {$this->join} {$this->where} {$this->group} {$this->order} {$this->limit}";
                break;
            case 'delete':
                $sql = "delete from  {$this->table} {$this->where} {$this->limit}";
                break;
        }

        return $sql;
    }
    /**
     * 获取单挑记录
     * @return null | \stdClass
     */
    public function SelectSingle()
    {
        if(!$this->sql)
            $sql = $this->CombinationSql('select');
        else
            $sql = $this->sql;
        $this->pdo_statement = $this->pdo->prepare($sql);
        $this->pdo_statement->execute();
        $result =  $this->pdo_statement->fetch(\PDO::FETCH_OBJ);
        if (empty($result)) {
            return null;
        }
        $this->SetPropertyDataType($this->pdo_statement, $result);
        return $result;
    }

    /*
     * 插入数据
     * @param string $table 表名
     * @param array $data  插入数据
     * @return   404 没有表  302 没有数据
     */
    public function Insert($table, array &$data)
    {
        $this->table = $table;
        if(!$this->table)
            return 404;
        if(!empty($data))
            return 302;
        $sql = $this->CombinationSql('insert',$data);
        $statement = $this->pdo->prepare($sql);
        $statement->execute();
        return $this->pdo->lastInsertId();
    }
    /*
     *编辑数据
     * @param string $table 表名
     * @param array $data  插入数据
     * @param string $where  修改条件
     * @return  404 没有表  302 没有数据
     */
    public function Update($table, array &$data, $where)
    {
        $this->table = $table;
        if(!$this->table)
            return 404;
        if(!empty($data))
            return 302;
        $this->where = " where {$where}";
        $sql = $this->CombinationSql('update',$data);
        $statement = $this->pdo->prepare($sql);
        $statement->execute();
        return $this->pdo->lastInsertId();
    }
    /*
     * 执行语句
     * @param string $sql sql语句
     * @return  404 没有表  302 没有数据
     */
    private function Query($sql, $column_name = '', $is_unique = true)
    {
        if(!$sql)
            return 404;
        $this->pdo_statement = $this->pdo->prepare($sql);
        $this->pdo_statement->execute();
        $result = array();
        while (($object = $this->pdo_statement->fetch(\PDO::FETCH_OBJ))) {
            $this->SetPropertyDataType($this->pdo_statement, $object);
            if (empty($column_name)) {
                $result[] = $object;
            } else {
                if (!$is_unique) {
                    if (!isset($result[$object->$column_name])) {
                        $result[$object->$column_name] = array();
                    }
                    $result[$object->$column_name][] = $object;
                } else {
                    $result[$object->$column_name] = $object;
                }
            }
        }
        return $result;
    }
    /**
     * 根据数据库字段的信息设置获取的对象实例的属性的数据类型
     * @param \PDOStatement $pdo_statement
     * @param \stdClass $object 设置属性的实例
     */
    private function SetPropertyDataType(\PDOStatement &$pdo_statement, \stdClass &$object)
    {
        if (!empty($object)) {
            $convertors = $this->GetConvertor($pdo_statement);

            foreach ($convertors as $key => $value) {
                $object->$key = $value($object->$key);
            }
        }
    }

    /**
     * 获取当前数据库列的对应的数据转换信息
     * @param \PDOStatement $pdo_statement
     * @return array
     */
    private function GetConvertor(\PDOStatement &$pdo_statement)
    {
        $convertors = array();
        $column_count = $pdo_statement->columnCount();
        $offset = -1;
        while ($offset++ < $column_count) {
            $column_meta = $pdo_statement->getColumnMeta($offset);
            if ($column_meta) {
                $value = null;
                switch ($column_meta['native_type']) {
                    case 'TINY':
                        $value = $column_meta['len'] === 1 ? 'boolval' : 'intval';
                        break;
                    case 'LONGLONG':
                    case 'SHORT':
                    case 'LONG':
                    case 'INT24':
                    case 'BIT':
                        $value = 'intval';
                        break;
                    case 'FLOAT':
                    case 'DOUBLE':
                    case 'NEWDECIMAL':
                        $value = 'floatval';
                        break;
                }
                if (!empty($value)) {
                    $convertors[$column_meta['name']] = $value;
                }
            }
        }

        return $convertors;
    }

    /**
     * 返回多条记录
     * @param string $column_name MYSQL中的列名，设置此值后返回的数据将以该列的值座位key
     * @param bool $is_unique $column_name对应的列名是否具有唯一值约束
     * @return array
     */
    public function SelectMulti($column_name = '', $is_unique = true)
    {
        $sql = $this->CombinationSql('select');
        $this->pdo_statement = $this->pdo->prepare($sql);
        $this->pdo_statement->execute();
        $result = array();
        while (($object = $this->pdo_statement->fetch(\PDO::FETCH_OBJ))) {
            $this->SetPropertyDataType($this->pdo_statement, $object);
            if (empty($column_name)) {
                $result[] = $object;
            } else {
                if (!$is_unique) {
                    if (!isset($result[$object->$column_name])) {
                        $result[$object->$column_name] = array();
                    }
                    $result[$object->$column_name][] = $object;
                } else {
                    $result[$object->$column_name] = $object;
                }
            }
        }
        return $result;
    }

    /**
     * 根据变量值判断其PDO数据类型
     * @param $value 值
     * @return int PDO 数据类型
     */
    public function GetPDOParamType($value)
    {
        switch (gettype($value)) {
            case 'integer':
                return \PDO::PARAM_INT;
            case 'boolean':
                return \PDO::PARAM_BOOL;
            default:
                return \PDO::PARAM_STR;
        }
    }

    /**
     * 生成一个请求参数的实例
     * @param string $key 名称， 对应数据库字段名
     * @param mixed $value 值
     * @param int $data_type PDO数据类型
     * @return \stdClass
     */
    public function GenerateQueryClass($key, $value, $data_type = 0)
    {
        $query = new \stdClass();
        $query->key = $key;
        $query->value = $value;
        $query->dt = $data_type == 0 ? $this->GetPDOParamType($value) : $data_type;
        return $query;
    }
}