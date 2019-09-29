<?php
class BaseError
{
    protected $code = 0;
    protected $msg = '';

    public function __construct($code, $msg)
    {
        $this->code = $code;
        $this->msg = $msg;
        $this->before($code, $msg);    
    }

    public function getCode()
    {
        return $this->code;     
    }

    public function getMsg()
    {
        return $this->msg;    
    }

    protected function before($code, $msg)
    {
        if($code <= 1000)
        {
           Assert::argEx('code 1 ~ 1000 for system.');
        }
    }
}
