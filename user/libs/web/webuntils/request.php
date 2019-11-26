<?php
class Request{

    public function getRequest($key, $val)
    {
        return array_key_exists($key, $_REQUEST) ? $_REQUEST[$key] : $val;
	}

	public function getData()
    {
		return $_REQUEST;
	}

    public function setInvoke($className, $funcName)
    {
        $this->do = [$className, $funcName]; 
    }

    public function filter()
    {
        
    }
}
