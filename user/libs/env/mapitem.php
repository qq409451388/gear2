<?php
class MapItem
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
}