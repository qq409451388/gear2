<?php
class Http
{
    private $request;
    private $response;

    public function __construct()
    {
        $this->request = Container::get(Request::class);
        $this->response = Container::get(Response::class);
    }

    public function startWebApp()
    {
        try{
            list($controller, $action) = $this->parseUri4WebApp();
            $this->invoke($controller, $action);
        }catch (\Exception $e){
            var_dump($e);
        }
    }

    public function send()
    {

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
        return [$className, $funcName];
    }

    private function invoke($class, $action)
    {
        $obj = new $class;
        return call_user_func_array([$obj, $action], []);
    }
}
