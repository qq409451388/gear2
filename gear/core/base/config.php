<?php
class Config
{
    private static $config;
    public static function get($key)
    {
        return self::$config[$key]??null;
    }

    public static function getAll($p){
        $pj = CORE_PATH.'/config/'.$p.'.json';
        return json_decode(file_get_contents($pj), true);
    }

    public static function setEnvInfo($arr){
        foreach($arr as $k => $v){
            self::$config[$k] = $v;
        }
    }
}
