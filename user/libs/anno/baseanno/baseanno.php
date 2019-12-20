<?php
class BaseAnno implements IAnno
{
    public const ANNO_CLASS = "CLASS";
    public const ANNO_METHOD = "METHOD";
    public const ANNO_PROPERTY = "PROPERTY";

    public function create(Array $item):IAnno{
        foreach($item as $key => $value){
            if(!is_array($value)){
                $value = [$value];
            }
            $this->save($key, $value);
        }
        return $this;
    }

    public function save(String $key, Array $val):void{
        $this->map[$key] = $val;
    }

    public function getClass(){
        return $this->map[self::ANNO_CLASS];
    }
    public function getMethod(){
        return $this->map[self::ANNO_METHOD];
    }
    public function getProperty(){
        return $this->map[self::ANNO_PROPERTY];
    }
}
