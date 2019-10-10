<?php
namespace gear\web;

class Response{
    public $code = 0;
    public $msg = '';
	public $data = [];

	public function setResponse($name, $value){
		$this->data[$name] = $value;
	}

	public function getData(){
		return $this->data;
	}

    public function getCode()
    {
        return $this->code;    
    }

    public function getMsg()
    {
        return $this->msg; 
    }
}
