<?php
class SystemError extends BaseError
{
    public function __construct($code, $msg)
    {
        parent::__construct($code, $msg); 
    }

    protected function before($code, $msg)
    {
        
    }
}
