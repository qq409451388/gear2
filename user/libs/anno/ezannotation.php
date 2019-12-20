<?php
class EzAnnotation
{
    private static $ins;
    private $list;
    private $hash;

    public static function create():EzAnnotation{
        if(!self::$ins instanceof EzAnnotation){
            $list = Config::get("anno");
            if(!self::check($list)){
                DBC::throwEx("[EzAnnotation Exception]Unknow Anno Config");
            }
            self::$ins = new self();
            self::$ins->list = $list;
        }
        return self::$ins;
    }

    public function init(){
        foreach($this->list as $annoName => $annoItem){
            $annoObj = $this->initAnnoObj($annoName, $annoItem);
            $this->addAnnotation($annoName, $annoObj);
        }
        return $this;
    }

    private static function check(Array $list){
        foreach($list as $item){
            if( !isset($item[BaseAnno::ANNO_CLASS]) ||
                !isset($item[BaseAnno::ANNO_METHOD]) ||
                !isset($item[BaseAnno::ANNO_PROPERTY]) ) {
                return false;
            }
        }
        return true;
    }

    private function initAnnoObj($annoName, $annoItem):IAnno{
        $anno = new $annoName();
        if(!$anno instanceof BaseAnno){
            DBC::throwEx("[EzAnnotation Exception]Unknow Anno {$annoName}");
        }
        return $anno->create($annoItem);
    }

    public function addAnnotation(String $name, Ianno $anno){
        if(null != $this->hash[$name]){
            return;
        }
        $this->hash[$name] = $anno;
    }

    public function getAnno($annoName):BaseAnno{
        return $this->hash[$annoName];
    }

    public function match(String $annoName, ReflectionClass $reflection){
        $className = $reflection->getName();
        $classDoc = $reflection->getDocComment();
        if($classDoc){
            $classTemp = $this->getAnno($annoName)->getClass();
            foreach($classTemp as $temp){
                preg_match($temp, $classDoc, $classMatch);
            }
        }

        $methods = $reflection->getMethods();
        $methodTemp = $this->getAnno($annoName)->getMethod();
        foreach($methods as $method){
            $methodDoc = $method->getDocComment();
            $methodName = $method->getName();
            if(!$methodDoc){
                continue;
            }
            foreach($methodTemp as $temp){
                preg_match($temp, $methodDoc, $match);
                if(!empty($match)){
                    $annoItem = new AnnoItem;
                    $annoItem->class = $className;
                    $annoItem->method = $methodName;
                    $annoItem->classMatch = end($classMatch);
                    $annoItem->methodMatch = end($match);
                    $this->getAnno($annoName)->saveItem($annoItem);
                }
            }
        }
    }
}
