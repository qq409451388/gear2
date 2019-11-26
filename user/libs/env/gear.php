<?php
class Gear
{
    private $objects = [];
    private $mapper;

    public function run(Array $hash){
        $this->mapper = Mapper::init();
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

    private function getAnnotation(ReflectionClass $reflection){
        $doc = $reflection->getDocComment();
        if(!$doc){
            return;
        }
        $matched = preg_match('/@RequestMapping\(\"\s*([^\s]*)\"\)/i', $doc, $classMatch);
        if(!$matched){
           return;
        }
        $methods = $reflection->getMethods();
        foreach($methods as $method){
            $doc = $method->getDocComment();
            if(!$doc){
                continue;
            }
            $matched = preg_match('/@GetMapping\(\"\s*([^\s]*)\"\)/i', $doc, $tmpMethodMatch);
            if(!$matched){
                continue;
            }
            $tmp = $classMatch[1].$tmpMethodMatch[1];
            Logger::console("[create mapping]".$tmp);
            $this->saveMapping($tmp, new MapItem($reflection->getName(), $method->getName()));
        }
    }

    private function registerDoc(ReflectionClass $reflection){
        $this->getAnnotation($reflection);
    }

    private function saveObject($key, $val):void{
        $this->objects[$key] = $val;
    }

    private function getObject($key){
        return $this->objects[$key] ?? null;
    }

    private function saveMapping($key, MapItem $val):void{
        $this->mapper->save($key, $val);
    }

    public function getMapping($key){
        return $this->mapper->get($key);
    }

    public function invokeMethod(MapItem $item, Array $params):String{
        $obj = $this->getObject(strtolower($item->getService()));
        if(null == $obj){
            return '{}';
        }
        return call_user_func_array([$obj,$item->getMethod()], $params)->toJson();
    }
}