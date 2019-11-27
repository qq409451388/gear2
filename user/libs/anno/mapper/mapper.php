<?php
class Mapper implements IAnno
{
    private static $ins;
    private $mapping = [];
    public static function init():IAnno{
        if(!self::$ins instanceof Mapper){
            self::$ins = new self();
        }
        return self::$ins;
    }

    public function saveAll($resClass, $resMethod, $className){
        foreach($resMethod as $methodName => $item){
            $key = $resClass.$item;
            $mapItem = new MapItem($className, $methodName);
            $this->save($key, $mapItem);
        }
    }

    public function save(String $key, AnnoItem $val):void{
        $this->mapping[$key] = $val;
    }

    public function get($key):AnnoItem{
        return $this->mapping[$key] ?? new NullAnnoItem();
    }
}
