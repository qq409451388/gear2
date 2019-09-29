<?php
class App1{
	protected $interceptors;

	public static function init()
    {
		return new self();
	}

	public function run()
    {
		$this->doSthWhenException();
		$interceptors = $this->loadInterceptors();
		$urlParsed = $this->doParseUri();
        if(empty($urlParsed) || count($urlParsed) != 3){
            Assert::exception('解析url失败！'); 
        }
		$this->invokeBefore($interceptors, $urlParsed);
        $res = $this->invokeAction($urlParsed[0], $urlParsed[1], $urlParsed[2]);
		if(1 == $res){
			return;
		}
		$this->doShowTemplate();
		$this->invokeAfter($interceptors, $urlParsed);
	}

    public function runApi()
    {
        $this->doSthWhenException();
        ob_start();
        try
        {
            $interceptors = $this->loadInterceptors();
            $urlParsed = $this->doParseUri();
            if(empty($urlParsed) || count($urlParsed) != 3){
                Assert::exception('解析url失败！'); 
            }

            $this->invokeBefore($interceptors, $urlParsed);
            $this->invokeAction($urlParsed[0], $urlParsed[1], $urlParsed[2]);
        }
        catch(LogicEx $le)
        {
            $responseObj = new Response();
            $responseObj->code = $le->getCode();
            $responseObj->msg = $le->getMessage();
            error_log(print_r($le, true)."\n", 3, '/tmp/myerror.log');
            echo DataTransfer::toJson($responseObj);
            return; 
        }
        catch(RuntimeEx $re)
        {
            $responseObj = new Response();
            $responseObj->code = -1;
            $responseObj->msg = $re->getMessage();
            error_log(print_r($re, true)."\n", 3, '/tmp/myerror.log');
            echo DataTransfer::toJson($responseObj);
            return; 

        }
        catch(Exception $e)
        {
            $responseObj = new Response();
            $responseObj->code = -1;
            $responseObj->msg = 'system error.';
            echo DataTransfer::toJson($responseObj);
            error_log(print_r($e, true)."\n", 3, '/tmp/myerror.log');
            return; 
        }
		$this->invokeAfter($interceptors, $urlParsed);
        echo DataTransfer::toJson(Loadder::get('Response'));
		$res = ob_get_contents();
		ob_end_clean();
		echo $res;

    }

    public function runService()
    {
		$this->doSthWhenException();
		$urlParsed = $this->doParseUri4Svc();
        $res = $this->invokeMethod($urlParsed[0], $urlParsed[1], $urlParsed[2]);
        $res = $res??[];
        echo json_encode($res);
    }

	public function doParseUri()
    {
		$parseArr = parse_url($_SERVER['REQUEST_URI']);
		$path = array_filter(explode('/', $parseArr['path']));

		$params = empty($parseArr['query']) ? [] : explode('&', $parseArr['query']);
		$end = end($path);
		$prev = prev($path);

		$funcName = empty($end) ? 'index' : $end;
		$className = empty($prev) ? 'indexcontroller': $prev.'controller';
		if(!class_exists($className)){
			Assert::exception('controller不存在！'.$className);
		}

        $funcName = strtolower($funcName);
        $funcNames = array_map(function($v){
            return strtolower($v);
        }, get_class_methods($className));
		if(!in_array($funcName, $funcNames)){
			Assert::exception('action不存在！'.$funcName);
		}
        return [$className, $funcName, $params];
	}

    public function doParseUri4Svc()
    {
        $parseArr = parse_url($_SERVER['REQUEST_URI']);
        $query = $parseArr['query']??[];
        if(empty($query))
            Assert::exception('params null');
        $options = [];
        parse_str($query, $options);
        $this->checkOptions($options);

        $className = strtolower($options['service']);
        $funcName = strtolower($options['method']);
        $params = empty($options['params']) ? [] : json_decode($options['params'], true);

        return [$className, $funcName, $params];
    }

    private function checkOptions($options)
    {
        if(empty($options['system']) || empty($options['service']) || empty($options['method']) || !isset($options['params']))    
            Assert::exception('params empty');
    }

    protected function invokeAction($className, $funcName, $params)
    {
        $controller = new $className($className, $funcName);

		Loadder::set('controller', $controller);
 		$func = [$controller, $funcName];
        return call_user_func_array($func, $params);
    }

    protected function invokeMethod($className, $funcName, $params)
    {
        $class = new $className;
        $bool = strpos($className, 'service');
        if(is_bool($bool))
            Assert::exception('no service named '.$className);
 		$func = [$class, $funcName];
        try
        {
		    return call_user_func_array($func, $params);
        }
        catch(Exception $e)
        {
            
        }
    }

	protected function doSthWhenException()
    {
		register_shutdown_function(function(){
			$error = error_get_last();
			if(
				isset($error['type']) &&
				in_array($error["type"], array(E_ERROR,E_PARSE,E_CORE_ERROR,E_COMPILE_ERROR,E_USER_ERROR,E_RECOVERABLE_ERROR))
			){
				if(EnvSetup::isDev() && !EnvSetup::isShowError()){
					extract($error);
					$all = Loadder::getAll();
					extract($all);
					ob_start();
					include(EnvSetup::exceptionUrl());
					ob_flush();
				}elseif(!EnvSetup::isDev()){
					header("Status: 503 DOA");
					//header('Location: '.EnvSetup::_404Url());
				}
			}
		});
	}

	protected function doShowTemplate()
    {
		$template = Loadder::get('controller')->template;
		if(!file_exists($template)){
			Assert::exception('模板文件不存在！');
		}
		extract(Loadder::get('Response')->getData());
		ob_start();
		include($template);
		/*$res = ob_get_contents();
		ob_end_clean();
		echo $res;*/
		ob_flush();
	}

	protected function setInspectors()
    {
		$this->interceptors = [];
	}

	protected function loadInterceptors()
    {
		$this->setInspectors();
		if(!empty($this->interceptors)){
			foreach($this->interceptors as $interceptor){
				$interceptors[] = new $interceptor;
			}
			return $interceptors;
		}
		return [];
	}

	protected function invokeBefore($interceptors, $urlParsed)
    {
		if(empty($interceptors)){
			return;
		}
		foreach($interceptors as $interceptor){
            if($this->dealFilter($urlParsed[0], $urlParsed[1], $interceptor->beforeFilter())){
                continue;    
            }
			$interceptor->before();
		}
	}

    protected function dealFilter($c, $a, $map)
    {
        $actions = empty($map[$c]) ? [] : $map[$c];
        if(empty($actions))
        {
            return false;    
        }
        return in_array($a, $actions);
    }

	protected function invokeAfter($interceptors, $urlParsed)
    {
		if(empty($interceptors)){
			return;
		}
		foreach($interceptors as $interceptor){
            if($this->dealFilter($urlParsed[0], $urlParsed[1], $interceptor->afterFilter())){
                continue;    
            }
			$interceptor->after();
		}
	}
}
