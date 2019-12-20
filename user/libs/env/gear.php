<?php
class Gear
{
    private $objects = [];
    private $annotation;

    public function run(Array $classess){
        //初始化注解模板
        $this->annotation = EzAnnotation::create()->init();
        //初始化对象
        $this->initObjects($classess);
        return $this;
    }

    private function initObjects($classess):void{
        foreach($classess as $class) {
            $object = $this->createObject($class);
            $this->saveObject($class, $object);
        }
    }

    /**
     * create a obj if none in objects[]
     * @param $class
     * @return Object
     */
    private function createObject($class):Object{
        if(null != $this->getObject($class)){
            Logger::console("[depend object]++{$class}");
            return $this->getObject($class);
        }
        try {
            Logger::console("[create object]{$class}");
            $reflection = new ReflectionClass($class);
            $this->matchMapping($reflection);
        } catch (ReflectionException $e) {
            DBC::throwEx("[create objects exception]{$e->getMessage()}");
        }
        $dependents = $this->getConstructObject($reflection);
        return $reflection->newInstanceArgs($dependents);
    }

    private function getConstructClass(ReflectionClass $reflection):Array{
        $construct = $reflection->getConstructor();
        return null == $construct ? [] : $construct->getParameters();
    }

    //创建注入对象
    private function getConstructObject(ReflectionClass $reflection):Array{
        $classes = $this->getConstructClass($reflection);
        $dependents = [];
        foreach($classes as $class){
            $dependents[] = $this->createObject($class->getName());
        }
        return $dependents;
    }

    private function saveObject($key, $val):void{
        $this->objects[$key] = $val;
    }

    private function getObject($key){
        $key = strtolower($key);
        return $this->objects[$key] ?? null;
    }

    public function matchMapping(ReflectionClass $reflection){
        assert($this->annotation instanceof EzAnnotation);
        $this->annotation->match("Mapper", $reflection);
    }

    public function getMapping($path){
        assert($this->annotation instanceof EzAnnotation);
        return $this->annotation->getAnno("Mapper")->match($path);
    }

    //TODO
    public function invokeInterceptor():bool{
        return true;
    }

    public function invokeMethod($item, Array $params):String{
        $obj = $this->getObject(strtolower(current($item)));
        if(null == $obj){
            return EzHttpResponse::EMPTY_RESPONSE;
        }
        if(!$this->invokeInterceptor()){
            return EzHttpResponse::EMPTY_RESPONSE;
        }
        return call_user_func_array([$obj,end($item)], $params)->toJson();
    }
}
