<?php
class Env
{
    public static function isDev(){
        return self::get() == 'DEV';
    }

    public static function getDomain(){
        $host = Config::get('host') == '0.0.0.0' ? 'localhost' : Config::get('host');
        $port = Config::get('port');
        return 'http://'.$host.':'.$port.'/';
    }

    public static function get(){
        return strtoupper(ENV);
    }
}