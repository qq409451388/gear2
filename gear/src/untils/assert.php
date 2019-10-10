<?php
namespace gear\untils;

use gear\exception\LogicEx;
use gear\exception\InvalidArgumentEx;
use gear\exception\RuntimeEx;

class Assert
{
    const DEFAULT_MSG = '与预期不符！';

    public static function false($arg, $msg = self::DEFAULT_MSG)
    {
        if ($arg) {
            self::exception($msg);
        }
    }

    public static function true($arg, $msg = self::DEFAULT_MSG)
    {
        if (!$arg) {
            self::exception($msg);
        }
    }

    public static function equalsTrue($expect, $actually, $msg = self::DEFAULT_MSG)
    {
        if ($expect !== $actually) {
            self::exception($msg);
        }
    }

    public static function equalsFalse($expect, $actually, $msg = self::DEFAULT_MSG)
    {
        if ($expect == $actually) {
            self::exception($msg);
        }
    }

    public static function equalsArray($expect, $actually, $msg = self::DEFAULT_MSG)
    {
        if (!empty(array_diff($expect, $actually)) || !empty(array_diff($actually, $expect))) {
            self::exception($msg);
        }
    }

    public static function equalsString(String $expect, String $actually, $msg = self::DEFAULT_MSG)
    {
        if ($expect != $actually) {
            self::exception($msg);
        }
    }

    public static function isNum($num, $msg = self::DEFAULT_MSG)
    {
        if (!is_numeric($num)) {
            self::exception($msg);
        }
    }

    public static function emptyArray($arg, $msg = self::DEFAULT_MSG)
    {
        if (!is_array($arg) || !empty($arg)) {
            self::exception($msg);
        }
    }

    public static function notEmptyArray($arg, $msg = self::DEFAULT_MSG)
    {
        if (!is_array($arg) || empty($arg)) {
            self::exception($msg);
        }
    }

    public static function exception($msg, $code = -1)
    {
        throw new \Exception($msg, $code);
    }

    public static function argEx($msg, $code = -1)
    {
        throw new InvalidArgumentEx($msg, $code);
    }

    public static function logicEx($msg, $code = -1)
    {
        throw new LogicEx($msg, $code);
    }

    public static function runtimeEx($msg, $code = -1)
    {
        throw new RuntimeEx($msg, $code);
    }
}
