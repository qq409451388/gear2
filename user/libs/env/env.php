<?php
class Env
{
    public static function isDev(){
        return ENV == 'dev';
    }
}