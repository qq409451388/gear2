<?php
class App extends Container{
    private $interceptors = [];

    protected $binds = [
        'http' => null,
        'tracer' => null,
        'config' => null
    ];

    private $error = [];

    /*
     * $config project config path
     */
    public function __construct($config = ''){
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
            $this->http->start();
            $this->http->send();
            $this->invokeAfter();
        }catch (RuntimeEx $e){
            $this->error = error_get_last();
            $this->dealRunTime();
        }catch (Exception $e){
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
        $interceptors = $this->config->interceptors;
        foreach($interceptors as $interceptor){
            $this->interceptors[] = new $interceptor;
        }
    }

    private function invokeBefore(){
        foreach($this->interceptors as $interceptor){
            if(!$interceptor instanceof IBaseInterceptor){
                Assert::runtimeEx("[Interceptor]Unknow Class $interceptor");
            }
            $interceptor->before();
        }
    }

    private function invokeAfter(){
        foreach($this->interceptors as $interceptor){
            if(!$interceptor instanceof IBaseInterceptor){
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
        return $this->get($name);
    }

    public function __destruct(){
        $this->tracer->finish();
        $this->tracer->log('app consume');
    }
}