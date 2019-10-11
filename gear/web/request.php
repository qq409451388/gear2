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

    public function getTemplate()
    {
        $c = $this->do[0];
        $c = str_replace('controller', '', $c);
        $a = $this->do[1];
        $path = SRC_PATH.'templates/'.$c.'/'.$a.'.php';
        if(!file_exists($path)){
           Assert::runtimeEx('[Request]unknow template'); 
        }
        return $path;
    }
}
