<?php
class MySqlSE implements IDbSe
{
    private $conn;
    private $mysqli;
    private $trace;

    public function __construct(String $host, String $user, String $pwd, String $database)
    {
        $this->init($host, $user, $pwd, $database);
    }

    private function init(String $host, String $user, String $pwd, String $database)
    {/*{{{*/
        if(empty($database))
        {
            DBC::throwEx('��Mysql Exception�� unselect db.', -1);
        }
        $this->mysqli = mysqli_init();
        $this->conn = $this->mysqli->real_connect($host, $user, $pwd, $database);
        if(0 != $this->mysqli->connect_errno)
        {
            DBC::throwEx('��Mysql Exception��'.$this->mysqli->connect_error, $this->mysqli->connect_errorno);
        }
        $this->mysqli->set_charset('gbk');
        $this->trace = new Trace();
        return $this;
    }/*}}}*/

    /*
    * ����list�е�һ�е�����
    * return Map
    */
    public function findOne(String $sql, Array $binds = [])
    {/*{{{*/
        $res = $this->query($sql, $binds);
        return empty($res) ? [] : current($res);
    }/*}}}*/

    /*
    * ����list�е�ĳһ��
    * return List
    */
    public function queryColumn(String $sql, Array $binds, String $column)
    {/*{{{*/
        $res = $this->query($sql, $binds);
        return array_column($res, $column);
    }/*}}}*/

    /*
    * ��list����ָ��key��val��ϳ�map
    * ����val��ʾ�������ӽ����Ϊval
    * keyֵҪΨһ
    * return Map
    */
    public function queryHash(String $sql, Array $binds, String $key, String $val = null)
    {/*{{{*/
        $res = $this->query($sql, $binds);
        return array_column($res, $val, $key);
    }/*}}}*/

    /*
    * ��list����ֵ����ָ��key���з���
    * return 2-Dimensional Array
    */
    public function queryGroup(String $sql, Array $binds, String $groupBy, String $val = ' ')
    {/*{{{*/
        $list = $this->query($sql, $binds);
        $res = [];
        foreach($list as $item)
        {
            if(empty($val))
            {
                $res[$item[$groupBy]][] = $item;
            }
            else
            {
                $res[$item[$groupBy]][] = $item[$val];
            }
        }
        return $res;
    }/*}}}*/

    /*
    * ����ָ����ֵ�������ؽ������һ��ʱ
    * return String
    */
    public function queryValue(String $sql, Array $binds, String $val)
    {/*{{{*/
        $res = $this->query($sql, $binds);
        $cur = current($res);
        return $cur[$val] ?? '';
    }/*}}}*/

    private function buildSql($sqlTemplate, $binds = [])
    {/*{{{*/
        if(empty($binds))
            return $sqlTemplate;
        foreach($binds as $key => $val)
        {
            if(is_array($val))
            {
                $tmp = '';
                foreach($val as $v)
                {
                    $tmp .= '"'.$v.'",';
                }
                $val = trim($tmp, ',');
            }
            else
            {
                $val = '"'.$val.'"';
            }
            $sqlTemplate = str_replace($key, $val, $sqlTemplate);
        }
        return $sqlTemplate;
    }/*}}}*/

    /*
    * �������ݿ��ѯ���
    * return List
    */
    public function query(String $sqlTemplate, Array $binds = [])
    {/*{{{*/
        $this->trace->start();
        $sql = $this->buildSql($sqlTemplate, $binds);
        $query = $this->mysqli->query($sql);
        $this->trace->log($sql, __CLASS__);
        if (0 != $this->mysqli->errno) {
            $msg = '��Mysql Exception��code��' . $this->mysqli->errno . '��msg��' . $this->mysqli->error;
            DBC::throwEx($msg, $this->mysqli->errno);
        }

        //for insert update delete
        if (is_bool($query))
        {
            return $query;
        }
        return $query ? $query->fetch_all(MYSQLI_ASSOC) : [];
    }/*}}}*/

    public function startTransaction()
    {/*{{{*/
        $this->query('start transaction');
    }/*}}}*/

    public function commit()
    {/*{{{*/
        $this->mysqli->commit();
    }/*}}}*/

    public function rollBack()
    {/*{{{*/
        $this->mysqli->rollBack();
    }/*}}}*/

    public function __destruct()
    {/*{{{*/
        if(!is_null($this->mysqli))
        {
            $this->mysqli->close();
        }
    }/*}}}*/
}
