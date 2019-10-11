<?php
class IndexController extends BaseController
{
    public function index()
    {
        usleep(123676);
        $this->setResponse('title', 'HelloWorld');        
    }
}
