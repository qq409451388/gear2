<?php
class DB{
    private static $ins = null;
    private $map = [];
	private $dbCon = [];
	private $sysHash = [];

    private function getSysName($database)
    {/*{{{*/
        if(empty($this->sysHash[$database]))
            DBC::throwEx('【Mysql Exception】null database:'.$database);
        return $this->sysHash[$database];
    }/*}}}*/

    private function getDbConfig($database)
    {
        $this->dbCon = Config::get('dbcon');
        $this->sysHash = Config::get('syshash');
        $sysName = $this->getSysName($database);
        return $this->dbCon[$sysName];
    }

    public static function get($database = ''):IDbSe
    {/*{{{*/
        if(null == self::$ins)
        {
            self::$ins = new self();
        }
        $dbConfig = self::$ins->getDbConfig($database);
        $se = self::$ins->map[$database] ?? null;
        if(!$se instanceof IDbSe)
        {
            //创建新实例
            $se = new MySqlSE($dbConfig['host'], $dbConfig['user'], $dbConfig['pwd'], $database);
            self::$ins->map[$database] = $se;
        }
        return $se;
    }/*}}}*/


}
