<?php
class Container{
    private $ins;
	protected $binds = [];

	protected function get($name):Container{
	    if(!array_key_exists($name, $this->binds)){
            Assert::argEx("[Container]no binds $name", -1);
        }
	    if(!$this->binds[$name] instanceof Container){
	        $this->binds[$name] = new $name;
        }
        return $this->binds[$name];
    }

}