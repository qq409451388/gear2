<?php
class MapItem implements AnnoItem
{
    private $service;
    private $method;

    public function __construct($s, $m){
        $this->service = $s;
        $this->method = $m;
    }

    public function getService(){
        return $this->service;
    }

    public function getMethod(){
        return $this->method;
    }

    public function isValid(): bool{
        return !empty($this->getService()) && !empty($this->getMethod());
    }
}