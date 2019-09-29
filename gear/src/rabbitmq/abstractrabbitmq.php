<?php
Abstract class AbstractRabbitMQ{
	protected $config = [];

	private $conn = null;
	private $channel = null;

	public function __construct($config){
		$this->config = DataTransfer::toArray($config);
	}

	protected function createConnect(){
		$rabbitMqConfig = $this->config['conn'];
		$this->conn = new AMQPConnection($rabbitMqConfig);
		if (!$this->conn->connect()) {
	    	Assert::exception("Cannot connect to the broker!\n");
		}
	}

	protected function createChannel(){
		$this->channel = new AMQPChannel($this->conn);
	}

	protected function createExChange($exChangeName, $exChangeType){
		$this->ex = new AMQPExchange($this->channel);
		$this->ex->setName($exChangeName);
		$this->ex->setType($exChangeType); //direct类型
		$this->ex->setFlags(AMQP_DURABLE); //持久化
		@$this->ex->declare();
	}

	protected function createQueue($queueName){
		$this->q[$queueName] = new AMQPQueue($this->channel);
		$this->q[$queueName]->setName($queueName);
		$this->q[$queueName]->setFlags(AMQP_DURABLE); //持久化
		@$this->q[$queueName]->declare();
	}

	protected function bindExChange($queueName, $exChangeName, $routingKey){
		$this->q[$queueName]->bind($exChangeName, $routingKey);
	}

	public function __destruct(){
		$this->conn->disconnect();
	}
}