<?php
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
            $this->http->initWebApp();
            $this->invokeAfter();
        }catch (RuntimeEx $e){
            $this->error = $e;
            $this->dealRunTime();
        }catch (\Exception $e){
            $this->error = error_get_last();
            $this->dealException();
        }finally{
            if(!empty($this->error)){
                Logger::exception(print_r($this->error, true));
            }
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
            return;
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
        $this->tracer->log($this->config->app.' app consume');
    }
}
