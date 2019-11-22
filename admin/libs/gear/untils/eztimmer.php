<?php
class EzTimmer{
	const ZERO_TIME = '0000-00-00 00:00:00';
	private $time;

	public static function now(){
		$self = new self();
		$self->time = time();
		return $self;
	}

	public function addSec(int $secs){
		$this->time += $sec;
		return $this;
	}

	public function addMinute(int $minutes){
		$this->time += $minutes * 60;
		return $this;
	}

	public function addHour(int $hours){
		$this->time += $hours * 60 * 60;
		return $this;
	}

	public function addDay(int $days){
		$this->time += $days * 60 * 60 * 24;
		return $this;
	}

	public function addYear(int $years){
		$this->time = strtotime($years.' year');
		return $this;
	}

	public static function isValid($data){
		return strtotime($data) ? true : false;
	}

	public static function getAgeByBirthDay($birthDay){
		try{
			if (!self::isValid($birthDay)){
				return 0;
			}
			$diffTime = time() - strtotime($birthDay);
			return (int)($diffTime/(60*60*24*365));
		}catch(Exception $e){
			Assert::exception('转换生日信息失败,msg:'.$e->getMessage());
			return 0;
		}
	}

	public static function getBirthDayByAge($age){
		try{
			if (!self::isValid($data)){
				return '';
			}
			return self::now()->addYear(-$age)->toShortString();
		}catch(Exception $e){
			Assert::exception('转换年龄信息失败,msg:'.$e->getMessage());
			return 0;
		}
	}

	public function toString(){
		return $this->toStringByFormat('Y-m-d H:i:s');
	}

	public function toShortString(){
		return date('Y-m-d', $this->time);
	}

	private function toStringByFormat($format = 'Y-m-d H:i:s'){
		return date($format, $this->time);
	}
}
