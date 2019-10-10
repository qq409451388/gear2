<?php
class Request{
    public function __construct()
    {

    }

    public function getRequest($key, $val){
        return array_key_exists($key, $_REQUEST) ? $_REQUEST[$key] : $val;
	}

	public function getData(){
		return $_REQUEST;
	}
}
