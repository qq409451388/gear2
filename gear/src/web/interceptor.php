<?php
namespace gear\web;
use gear\untils\Assert;
use gear\web\IBaseInterceptor;

class Interceptor implements IBaseInterceptor{
    public static function get(Array $interceptors){
        foreach($interceptors as $interceptor){
            if(!class_exists($interceptor)){
                Assert::runtimeEx("[Interceptor]Unknow Class $interceptor");
            }
            $n[] = new $interceptor;
        }
        return $n;
    }

    public function before(){}
    public function after(){}
}