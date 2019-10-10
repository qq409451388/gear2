<?php
class RabbitMQ extends AbstractRabbitMQ{

	public function init(){
		$this->createConnect();
		$this->createChannel();
		$exChangeName = $this->config['producer']['exchange']['name'];
		$exChangeType = $this->config['producer']['exchange']['type'];
		$this->createExChange($exChangeName, $exChangeType);
		foreach($this->config['consumer'] as $queueName => $item){
			$this->createQueue($queueName);
			$this->bindExChange($queueName, $item['exchange'], $item['routingKey']);
		}

		$msg = json_encode([1,2,3]);
		$this->ex->publish($msg, 'routingKey');
	}

	public function publish(){
		$this->createConnect();
		$this->createChannel();
		$exChangeName = $this->config['producer']['exchange']['name'];
		$exChangeType = $this->config['producer']['exchange']['type'];
		$a = $this->createExChange($exChangeName, $exChangeType);
	}

	public function consumer($queueName){
		$this->createConnect();
		$this->createChannel();
		$exChangeName = $this->config['producer']['exchange']['name'];
		$exChangeType = $this->config['producer']['exchange']['type'];
		$this->createExChange($exChangeName, $exChangeType);
		foreach($this->config['consumer'] as $queueName => $item){
			$this->createQueue($queueName);
			$this->bindExChange($queueName, $item['exchange'], $item['routingKey']);
		}
		$this->q[$queueName]->consume("callBack");
	}

	public function callBack(){
	}
}