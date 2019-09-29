<?php

/*
* 实现数据库的基础操作
*/

class Mysql extends DB
{
    private $conn;
    private $mysqli;
    private $db;

    public function __construct($name = 'default')
    {
        $config = Loadder::get('Config')->getConfig('dbconfig');
        if (empty($config)) {
            Assert::exception('no mysql config');
        }
        $config = $config->$name;
        if (empty($config)) {
            Assert::exception('no mysql config ' . $config);
        }
        $this->mysqli = mysqli_init();
        $this->conn = $this->mysqli->real_connect($config->host, $config->userName, $config->passWord);
        $this->mysqli->set_charset($config->charset);
    }

    public function selectDb($name)
    {
        $name = strtolower($name);
        $this->db = $name;
        $this->mysqli->select_db($name);
        return $this;
    }

    public function setCharset($charset)
    {
        $this->mysqli->set_charset($charset);
    }

    public function findById($tableName, $id)
    {
        $mysqlSyntax = new MySqlSyntax;
        $sql = $mysqlSyntax->column()->table($tableName)->where(['id' => $id])->buildSelect();
        return $this->query($sql);
    }

    public function findByIds($tableName, $ids)
    {
        $mysqlSyntax = new MySqlSyntax;
        $sql = $mysqlSyntax->column()->table($tableName)->where(['id' => $ids])->buildSelect();
        return $this->query($sql);
    }

    public function save($entity)
    {
        $mysqlSyntax = new MySqlSyntax;
        $arr = $entity->toArray();
        $sql = $mysqlSyntax->createField(array_keys($arr))->table($entity->getBindTable())->createValue(array_values($arr))->where(['id' => $entity->id])->buildCreate();
        $res = $this->mysqli->query($sql);
        $this->catchException();
        return $res;
    }

    public function update($entity)
    {
        $mysqlSyntax = new MySqlSyntax;
        $arr = $entity->toArray();
        $sql = $mysqlSyntax->updateColumn($arr)->table($entity->getBindTable())->where(['id' => $entity->id])->buildUpdate();
        $res = $this->mysqli->query($sql);
        $this->catchException();
        return $res;
    }

    public function delete($entity)
    {
        $mysqlSyntax = new MySqlSyntax;
        $sql = $mysqlSyntax->table($entity->getBindTable())->where(['id' => $entity->id])->buildDelete();
        $res = $this->mysqli->query($sql);
        $this->catchException();
        return $res;
    }


    public function query($sql = '')
    {
        $query = $this->mysqli->query($sql);
        $this->catchException();
        if (is_bool($query))
        {
            return $query;
        }
        return $query ? $query->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function queryValue($sql = '', $column = '')
    {
        $res = $this->query($sql);
        return empty($res) ? '' : current($res)[$column];
    }

    //异常情况
    private function catchException()
    {
        if (0 != $this->mysqli->errno) {
            $msg = '【Mysql Exception】code：' . $this->mysqli->errno . '，msg：' . $this->mysqli->error;
            Assert::exception($msg);
        }
    }

    public function __destruct()
    {
        $this->mysqli->close();
    }

}
