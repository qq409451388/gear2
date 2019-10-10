<?php
class Interceptor implements IBaseInterceptor{
    public static function get(Array $interceptors){
        $n = [];
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
