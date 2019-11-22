<?php
class Producer{
	private $config = [];
	private static $ins = [];
	private $mq;

	public static function getInstance($sysName){
		if(empty(self::$ins)){
			self::$ins[$sysName] = new self($sysName);
		}
		return self::$ins[$sysName];
	}

	private function __construct($sysName){
		$config = Loadder::get('Config');
		if(!$config instanceof Config){
			Assert::exception('no config has been loaded');
		}
		$this->config = $config->getConfig('rabbitMqConfig')->$sysName;
		$this->mq = new RabbitMQ($this->config);
	}

	public function publish(){
	}
}