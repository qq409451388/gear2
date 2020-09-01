<?php
class Request
{
    //get post mixed
    private $requestMethod = null;
    private $request = [];

    public function setRequest($key, $value){
        $this->request[$key] = $value;
    }

    public function get($key, $default=null){
        return isset($this->request[$key]) ? $this->request[$key] : $default;
    }

    public function filter(){

    }

    public function isEmpty(){
        return empty($this->request);
    }

    public function setRequestMethod($requestMethod){
        $this->requestMethod = $requestMethod;
    }
}