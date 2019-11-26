<?php
class DBC
{
    public static function throwEx($msg, $code = 0, $type = 'Exception')
    {
        throw new $type($msg, $code); 
    }
}
