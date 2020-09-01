<?php
class Gear
{
    public function init(Array $classess){
        //初始化对象
        $this->initObjects($classess);
        $this->buildRouter();
        return $this;
    }

    private function initObjects($classess){
        foreach($classess as $class) {
            $this->createObject($class);
        }
    }

    private function buildRouter(){
        $router = EzRouter::get();
        foreach(BeanFinder::get()->getAll() as $objName => $obj){
            $reflection = new ReflectionClass($obj);
            $reflectionMethods = $reflection->getMethods();
            foreach($reflectionMethods as $reflectionMethod) {
                $path = RouterAnno::get()->buildPath($reflection->getDocComment(), $reflectionMethod->getDocComment());
                if(empty($path)){
                    $path = $objName.'/'.$reflectionMethod->getName();
                }
                $router->setMapping($path, $objName, $reflectionMethod->getName());
            }
        }
    }

    public function judgePath($path):bool{
        return EzRouter::get()->judgePath($path);
    }

    public function disPatch($path, $request){
        $mapper = EzRouter::get()->getMapping($path);
        return $mapper->disPatch($request);
    }

    /**
     * create a obj if none in objects[]
     * @param $class
     * @return Object
     */
    private function createObject($class){
        try {
            Logger::console("[create object]{$class}");
            BeanFinder::get()->save($class, new $class);
        } catch (ReflectionException $e) {
            DBC::throwEx("[create objects exception]{$e->getMessage()}");
        }
    }

    public function invokeInterceptor():bool{
        return true;
    }

    public function invokeMethod($item, Array $params):String{
        $obj = BeanFinder::get()->pull(current($item));
        if(null == $obj){
            return EzHttpResponse::EMPTY_RESPONSE;
        }
        if(!$this->invokeInterceptor()){
            return EzHttpResponse::EMPTY_RESPONSE;
        }
        return call_user_func_array([$obj,end($item)], $params)->toJson();
    }
}