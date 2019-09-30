<?php
namespace gear;
use gear\untils\Assert;
class Config{
	private $map = [];

	public function __construct($config){
	    if(!empty($config) && !is_dir($config)){
	        Assert::argEx("[Config] is not dir $config");
        }
    }

    public function getAll(){
		return $this->map;
	}

	public function __get($name){
	    return $this->map[$name] ?? [];
    }
}