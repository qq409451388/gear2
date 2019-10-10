<?php
namespace gear\web;

use gear\Env;
use gear\Config;
use gear\Container;
use gear\untils\Assert;
use gear\untils\Tracer;
use gear\untils\Logger;
use gear\web\Interceptor;
use gear\web\Http;

class App
{
    private $interceptors = [];

    protected $binds = [
        'http' => Http::class,
        'config' => Config::class
    ];

    private $error = [];

    /*
     * $config project config path
     */
    public function __construct($config = ''){
        $this->tracer = new Tracer();
        $this->start();
        $this->config = new Config($config);
    }

    private function start(){
        $this->tracer->start();
    }

    public function initWebApp(){
        try{
            $this->registerInterceptors();
            $this->invokeBefore();
            $this->http->startWebApp();
            $this->http->send();
            $this->invokeAfter();
        }catch (RuntimeEx $e){
            $this->error = error_get_last();
            $this->dealRunTime();
        }catch (\Exception $e){
            $this->error = error_get_last();
            $this->dealException();
        }finally{
            Logger::exception($this->error);
        }
    }

    public function initService(){

    }

    public function initApi(){

    }

    private function registerInterceptors(){
        $this->interceptors = Interceptor::get($this->config->interceptors);
    }

    private function invokeBefore(){
        foreach($this->interceptors as $interceptor){
            $interceptor->before();
        }
    }

    private function invokeAfter(){
        foreach($this->interceptors as $interceptor){
            if(!$interceptor instanceof Interceptor){
                Assert::runtimeEx("[Interceptor]Unknow Class $interceptor");
            }
            $interceptor->after();
        }
    }

    private function dealRunTime(){
        if(Env::isDev()){
            extract($this->error);
            ob_start();
            include($this->exceptionUrl());
            ob_flush();
        }else{
            header("Status: 503 DOA");
            header('Location: '.$this->__404Url());
        }
    }

    private function dealException(){

    }

    private function __404Url(){

    }

    private function exceptionUrl(){

    }

    public function __get($name){
        $className = $this->binds[$name] ?? '';
        return Container::get($className);
    }

    public function __destruct(){
        $this->tracer->finish();
        $this->tracer->log('app consume');
    }
}
