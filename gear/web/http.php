<?php
namespace gear\web;

use gear\untils\Assert;

class Http
{
    public function start()
    {
        try{
            $this->parseUri();
        }catch (\Exception $e){
            var_dump($e);
        }
    }

    public function send()
    {

    }

    private function parseUri()
    {
        $parseArr = parse_url($_SERVER['REQUEST_URI']);
        $path = array_filter(explode('/', $parseArr['path']));

        $params = empty($parseArr['query']) ? [] : explode('&', $parseArr['query']);
        $end = end($path);
        $prev = prev($path);

        $funcName = empty($end) ? 'index' : $end;
        $className = empty($prev) ? 'indexcontroller': $prev.'controller';
        if(!class_exists($className)){
            Assert::runtimeEx('[Http]controller不存在！'.$className);
        }

        $funcName = strtolower($funcName);
        $funcNames = array_map(function($v){
            return strtolower($v);
        }, get_class_methods($className));
        if(!in_array($funcName, $funcNames)){
            Assert::runtimeEx('[Http]action不存在！'.$funcName);
        }
        return [$className, $funcName, $params];
    }
}
