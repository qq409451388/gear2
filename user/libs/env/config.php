<?php
class Config
{
    public static function get($config)
    {
        $filePath = BASE_PATH.'/config/'.$config.'.json';
        $json = file_get_contents($filePath);    
        return json_decode($json, true);
    }
}
