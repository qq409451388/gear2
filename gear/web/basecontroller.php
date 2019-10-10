<?php
Abstract class BaseController{
	protected $request;
	protected $response;
	const DIRECT_OUTPUT = 1;

	public function __construct($controller, $action){
		$this->response = Loadder::get('Response');
		$this->request = Loadder::get('Request');
		$this->controller = $controller;
		$this->action = $action;
		//TODO 自定义模板
		$this->template = $this->getTemplate();
	}

	public function setResponse($name, $value){
		$this->response->setResponse($name, $value);
	}

	private function getTemplate(){
		if(!empty($this->template)){
			return $this->template;
		}
		return $this->getTemplatePath();
	}

	public function getTemplatePath(){
		$webPath = str_replace('controller', '', $this->controller);
		return strtolower(WEB_PATH.'/templates/'.$webPath.'/'.$this->action.'.php');
	}

	public function getRequest($key, $val = ''){
		return $this->request->getRequest($key, $val);
	}

	public function getData(){
		return $this->request->getData();
	}

    public function getUserName(){
        return EzCookie::getUserName(); 
    }

    public function setData($data)
    {
        $this->response->data = $data;    
    }

    public function setErrorInfo(BaseError $err)
    {
        $this->response->code = $err->getCode();
        $this->response->msg = $err->getMsg();
    }
}
