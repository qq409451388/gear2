<?php
class Mapper
{
    private $mapping = [];
    public static function init(){
        return new self();
    }

    public function save($key, MapItem $val):void{
        $this->mapping[$key] = $val;
    }

    public function get($key){
        return $this->mapping[$key] ?? null;
    }
}