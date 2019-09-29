<?php
class BaseService{
	private $daos = [];
	protected function getSysCode()
    {
        $code = str_replace('Service', '', get_class($this));
        if(!empty($code))
        {
            return $code; 
        }
		Assert::exception('必须实现getSysCode方法');
	}

	public function getDao($code = '')
    {
		$code = empty($code) ? $this->getSysCode() : $code;
		$class = $code.'Dao';
		if(empty($this->daos[$code]) || !$this->daos[$code] instanceof $class){
			$this->daos[$code] = new $class;
		}
		return $this->daos[$code];
	}

	public function __call($funcName, $args)
    {
		if(strstr($funcName, 'WithCache')){
			$key = 'servicecache_'.$funcName.'_'.DataTransfer::toSimpleString($args);
			$redisData = EzRedis::getRedisClient()->get($key);
			if($redisData){
				return unserialize($redisData);
			}else{
				$funcName = str_replace('WithCache', '', $funcName);
				$res = call_user_func_array([$this, $funcName], $args);
				if(empty($res)){
					Assert::exception('接口调用失败！');
				}
				EzRedis::getRedisClient()->set($key, serialize($res), 86400);
				return $res;
			}
		}
		return call_user_func_array([$this->getDao(), $funcName], $args);
	}

}
