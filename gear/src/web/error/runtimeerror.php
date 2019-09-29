<?php
class RuntimeError extends BaseError
{
    public function __construct($code, $msg)
    {
        parent::__construct($code, $msg);
    }

    public function push()
    {
        Assert::runtimeEx($this->msg);    
    }
}
