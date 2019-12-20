<?php
class Config
{
    public static function get($config):Array
    {
        $filePath = BASE_PATH.'/config/'.$config.'.json';
        $json = file_get_contents($filePath);
        return empty($json) ? [] : json_decode($json, true);
    }
}
