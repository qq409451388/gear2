<?php
class Mapper extends BaseAnno implements IAnno
{
    protected $map = [];
    protected $match = [];
    public function saveItem(AnnoItem $annoItem){
        $key = trim($annoItem->classMatch.'/'.$annoItem->methodMatch, "/");
        $this->match[$key] = $annoItem->class."::".$annoItem->method;
    }

    public function match($key){
        if(empty($this->match[$key])){
            return [];
        }
        return explode("::", $this->match[$key]);
    }
}
