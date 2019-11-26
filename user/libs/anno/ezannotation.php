<?php
class EzAnnotation
{
    private $list = [];

    public function __construct(Array $list){
        $this->list = $list;
    }

    public static function init():EzAnnotation{
        $list = Config::get("anno");
        $newList = [];
        foreach($list as $obj => $annoList){
            $tmp = new AnnotationItem($obj, $annoList['class'], $annoList['method']);
            $newList[] = $tmp;
        }
        return new self($newList);
    }

    public function addAnnotation($obj, AnnotationItem $annotationItem){
        if(!empty($this->list[$obj])){
            $annotationItem->merge($this->list[$obj]);
        }
        $this->list[$obj] = $annotationItem;
    }

    public function getAllAnnotation(Reflector $reflection){
        foreach($this->list as $obj => $anno){
            $anno->setReflector($reflection);
            list($resClass, $resMethod) = $anno->deal();
            AnnoFactory::create($obj)->saveAll($resClass, $resMethod, $reflection->getName());
        }
    }
}