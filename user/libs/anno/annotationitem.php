<?php
class AnnotationItem
{
    private $obj;
    private $reflection;
    private $method;
    private $class;

    public function __construct($obj, Array $annoListClass, Array $annoListMethod){
        $this->obj = $obj;
        $this->class = $annoListClass;
        $this->method = $annoListMethod;
    }

    public function setReflector(Reflector $reflection){
        $this->reflection = $reflection;
    }

    public function deal():Array{
        $resClass = $resMethod = [];
        foreach($this->class as $item){
            $match = $this->getAnnotation($this->reflection, $item);
            if(!empty($match)){
                $resClass = end($match);
            }
        }
        $methodReflections = $this->reflection->getMethods();
        foreach($methodReflections as $methodReflection){
            foreach($this->method as $item){
                $match = $this->getAnnotation($methodReflection, $item);
                if(!empty($match)){
                    $resMethod[$methodReflection->getName()] = end($match);
                }
            }
        }

        return [$resClass, $resMethod];
    }

    private function getAnnotation(Reflector $reflection, String $pattern):Array{
        $doc = $reflection->getDocComment();
        if(!$doc){
            return [];
        }
        preg_match($pattern, $doc, $match);
        return $match;
    }

    //TODO merge
    public function merge(AnnotationItem $annotationItem){

    }
}