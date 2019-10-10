<?php
namespace gear\untils;
class Logger
{
    const LOG_PATH = LIB_PATH.'/logs/';
    //仅记录
    const TYPE_RECORD = 'record';
    //关键性数据储存
    const TYPE_DATA = 'data';
    //异常信息
    const TYPE_EXCEPTION = 'exception';

    public static function info($template, ...$args){
        $template = '【Info】'.$template;
        $res = self::matchTemplate($template, $args);
        self::write($res, self::TYPE_RECORD);
    }

    public static function warn($template, ...$args){
        $template = '【Warn】'.$template;
        $res = self::matchTemplate($template, $args);
        self::write($res, self::TYPE_RECORD);
    }

    public static function error($template, ...$args){
        $template = '【Error】'.$template;
        $res = self::matchTemplate($template, $args);
        self::write($res, self::TYPE_RECORD);
    }

    public static function exception($msg){
        self::write($msg, self::TYPE_EXCEPTION);
    }

    public static function save($msg, $name){
        self::write($msg, self::TYPE_DATA, $name);
    }

    private static function write($msg, $type, $fileName = ''){
        if(empty($msg)){
            return;
        }
        $dirPath = self::LOG_PATH.$type.'/';

        if(!is_dir($dirPath)) {
            mkdir($dirPath);
        }
        $ext = '.log';
        if(empty($fileName)) {
            $fileName = date('Y-m-d');
        }
        $filePath = $dirPath.$fileName.$ext;
        $fp = fopen($filePath, 'a');
        if(self::TYPE_RECORD == $type) {
            $msg = date('Y/m/d H:i:s  ').$msg;
        }
        fwrite($fp, $msg);
        fclose($fp);
    }

    private static function matchTemplate($template, $args){
        foreach($args as $arg) {
            $template = XString::str_replace_once('{}', $arg, $template);
        }
        return $template.PHP_EOL;
    }
}
