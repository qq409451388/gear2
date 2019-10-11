<?php
class Http
{
    private $request;
    private $response;
    const DIRECT_OUTPUT = 1;

    public function __construct()
    {
        $this->request = Container::get(Request::class);
        $this->response = Container::get(Response::class);
    }

    public function initWebApp()
    {
        $this->request->filter();
        $this->parseUri4WebApp();
        $this->invoke();
        $this->rander();
    }

    public function initApi()
    {
        $this->parseUri4WebApp();
        $res = $this->invoke();
        if(self::DIRECT_OUTPUT != $res){
            Assert::runtimeEx('[Http]no output'); 
        }
    }

    public function rander()
    {
        $template = $this->request->getTemplate();
        extract($this->response->getData());
        ob_start();
        include($template);
        ob_flush();
    }

    public function invoke()
    {
        if(empty($this->request->do)){
            Assert::runtimeEx('[Request]empty do');    
        }
        $className = $this->request->do[0];
        $funcName = $this->request->do[1];
        $obj = new $className;
        return call_user_func_array([$obj, $funcName], []);    
    }

    private function parseUri4WebApp()
    {
        if(empty($_SERVER['REQUEST_URI'])){
            Assert::runtimeEx("[Http]unknow uri");
        }
        $parseArr = parse_url($_SERVER['REQUEST_URI']) ?? '';
        $path = array_filter(explode('/', $parseArr['path']));
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
        $this->request->setInvoke($className, $funcName);
    }
}
