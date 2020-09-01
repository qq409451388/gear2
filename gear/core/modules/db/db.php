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
        $this->dbCon = Config::getAll('dbcon');
        if(empty(Env::get())){
            DBC::throwEx("[DB] Null Env");
        }
        $this->sysHash = Config::getAll('syshash')[Env::get()] ?? [];
        if(empty($this->dbCon) || empty($this->sysHash)){
            DBC::throwEx("[DB] Null DB Config");
        }
        $sysName = $this->getSysName($database);
        return $this->dbCon[$sysName];
    }

    public static function get($database = ''):IDbSe
    {/*{{{*/
        if(null == self::$ins){
            self::$ins = new self();
        }
        $dbConfig = self::$ins->getDbConfig($database);
        if(empty($dbConfig)){
            DBC::throwEx("[DB] UnKnow DB source : $database");
        }
        $se = self::$ins->map[$database] ?? null;
        if($se instanceof IDbSe && $se->isExpired()){
            Logger::console("[DB] expired and rebuild...");
            $se = null;
        }
        if(!$se instanceof IDbSe)
        {
            //创建新实例
            $se = new MySqlSE($dbConfig['host'], $dbConfig['user'], $dbConfig['pwd'], $database);
            self::$ins->map[$database] = $se;
        }
        return $se;
    }/*}}}*/
}
