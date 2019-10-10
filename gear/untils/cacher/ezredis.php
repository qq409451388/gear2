<?php
class EzRedis{
	const DEFAULT_EXPIRE_TIME = 7200;
	private $redis;

	public function __construct($config){
		$this->redis = new Redis();
		$this->connect($config);
	}

	public static function getRedisClient($name='default'){
		$config = Loadder::get('Config')->getConfig('redisconfig')->$name;
		if(null == Loadder::get($name)){
			$redis = new EzRedis($config);
			Loadder::set($name, $redis);
		}
		return Loadder::get($name);
	}

	public function __call($func, $args){
		switch($func){
			case 'set':
				if($this->checkNeedEncode($args[1])){
					$args[1] = json_encode($args[1]);
				}
				if(count($args) == 2){
					$args[] = self::DEFAULT_EXPIRE_TIME;
				}
				break;
			default:
				return call_user_func_array([$this, $func], $args);
		}
		return call_user_func_array([$this, $func], $args);
	}

	private function connect($config){
		if(empty($config->pwd)){
			$this->redis->connect($config->host, $config->port);
		}else{
			$this->redis->connect($config->host, $config->port, $config->pwd);
		}
	}

	private function set($k, $v, $expire){
		return $this->redis->set($k, $v, $expire);
	}

	private function get($k){
		return $this->redis->get($k);
	}

    private function del($k)
    {
        return $this->redis->del($k); 
    }

	private function getSource($k){
		return json_decode($this->redis->get($k), "true");
	}

	private function checkNeedEncode($obj){
		return is_array($obj) || is_object($obj);
	}
}
