<?php
class Gear
{
    private $objects = [];
    private $mapper;
    private $annotation;

    public function run(Array $hash){
        $this->mapper = Mapper::init();
        $this->annotation = EzAnnotation::init();
        $this->initObjects($hash);
        return $this;
    }

    private function initObjects($hash):void{
        foreach($hash as $class => $path) {
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
        Logger::console("[create object]{$class}");
        if(null != $this->getObject($class)){
            return $this->getObject($class);
        }
        try {
            $reflection = new ReflectionClass($class);
            $this->registerDoc($reflection);
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

    private function getConstructObject(ReflectionClass $reflection):Array{
        $classes = $this->getConstructClass($reflection);
        $dependents = [];
        foreach($classes as $class){
            $dependents[] = $this->createObject($class->getName());
        }
        return $dependents;
    }

    private function registerDoc(ReflectionClass $reflection){
        EzAnnotation::init()->getAllAnnotation($reflection);
    }

    private function saveObject($key, $val):void{
        $this->objects[$key] = $val;
    }

    private function getObject($key){
        return $this->objects[$key] ?? null;
    }

    public function getMapping($key){
        return $this->mapper->get($key);
    }

    //TODO
    public function invokeInterceptor():bool{
        return true;
    }

    public function invokeMethod(AnnoItem $item, Array $params):String{
        if(null == $item){
            return EzHttpResponse::EMPTY_RESPONSE;    
        }
        if(!$this->invokeInterceptor()){
            return EzHttpResponse::EMPTY_RESPONSE;
        }
        $obj = $this->getObject(strtolower($item->getService()));
        if(null == $obj){
            return EzHttpResponse::EMPTY_RESPONSE;
        }
        return call_user_func_array([$obj,$item->getMethod()], $params)->toJson();
    }
}
