<?php
class IdGenter{
	const PRE_FIX = "IDGENTER_";
	private static $ins = null;

	public static function getInstance(){
		if(null == self::$ins){
			self::$ins = new self();
			self::setDb();
		}
		return self::$ins;
	}

	private static function setDb(){
		$ms = new MySql();
		$ms->selectDb('system');
		Loadder::setDb('idgenter', $ms);
	}

	public function createId($objName = 'default', $count = 1){
		$idSet = $this->getIdSet($objName, $count);
		$curId = (String)$idSet['curId'];
		for($i=0;$i<$count;$i++){
			$ids[] = $curId++;
		}
		$idSet['curId'] = $curId;
		$this->updateIdSetFromCache($objName, $idSet);
		return 1 == $count ? current($ids) : $ids;
	}

	public function getIdSet($objName = 'default', $count = 1){
		$idSet = $this->getIdSetFromCache($objName, $count);
		if(empty($idSet)){
			$idSet = $this->loadIdSet($objName, $count);
		}else{
			if(!$this->judgeEnough($idSet, $count)){
				$idSet = $this->loadIdSet($objName, $count);
			}

		}
		return $idSet;
	}

	private function getIdSetFromDb($objName, $count){
		$sql = "select * from idgenter where objname = '".$objName."' limit 1;";
		$rows = Loadder::getDb('idgenter')->query($sql);
		$res[$objName] = current($rows);
		return $res;
	}

	private function dealIdSet(&$idSet, $objName, $count){
		$idSet['maxId'] = $idSet['curId'] + $idSet['step'];
	}

	private function getIdSetFromCache($objName, $count = 1){
		$key = self::PRE_FIX.$objName;
		return EzRedis::getRedisClient()->getSource($key);
	}

	private function loadIdSet($objName, $count){
		$idSetDb = $this->getIdSetFromDb($objName, $count);
		$tmpIdSet = [
			'curId' => $idSetDb[$objName]['id'],
			'maxId' => $idSetDb[$objName]['id'],
			'step' => $idSetDb[$objName]['step']
		];
		$this->dealIdSet($tmpIdSet, $objName, $count);
		$this->updateIdSetFromDb($objName, $tmpIdSet);
		$this->updateIdSetFromCache($objName, $tmpIdSet);
		return $tmpIdSet;
	}

	private function judgeEnough($idSet, $count){
		return $idSet['maxId'] >= ($idSet['curId'] + $count);
	}

	private function updateIdSetFromDb($objName, $idSet){
		$sql = "update idgenter set id = '".$idSet['maxId']."' where objname = '".$objName."'";
		Loadder::getDb('idgenter')->query($sql);
	}

	private function updateIdSetFromCache($objName, $idSet){
		$key = self::PRE_FIX.$objName;
		return EzRedis::getRedisClient()->set($key, $idSet);
	}
}
