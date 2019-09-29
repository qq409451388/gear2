<?php
class EntityFactory{
	private $dbSource;
	private $sourceName;
	private $cache;

	public function __construct()
    {
		$this->cache = EzRedis::getRedisClient();
	}

	private function setUpDb($source, $dbName)
    {
		if(empty(Loadder::get('db')[$source])){
			Loadder::setDb($source, new MySql($source));
		}
		$dbSource = Loadder::getDb($source);
		$dbSource->selectDb($dbName);
		return $dbSource;
	}

	public function find($entityName, $id, $onlyDb = false)
    {
        $entityKey = $this->getEntityKey($entityName, $id);
        if(!$onlyDb)
        {
            $tmp = $this->cache->get($entityKey);
			if($tmp){
				return unserialize($tmp);
			}
        }
		//要查询的实体，能拿到系统名，数据库连接名
		$entity = new $entityName;

		//数据库连接名，默认default
		$sourceName = $entity->getSourceName();
		//系统code对应数据库名
		$dbName = $entity->getSystemCode();
		//初始化数据库
		$dbSource = $this->setUpDb($sourceName, $dbName);
		
		$info = $dbSource->findById($entity->getBindTable(), $id);

		if(empty($info)){
			return new NullEntity();
		}
		$obj = $this->toObject($entityName, current($info));
		$this->cache->set($entityKey, serialize($obj));
		return $obj;
	}

	public function findList($entityName, $ids)
    {
		//要查询的实体，能拿到系统名，数据库连接名
		$entity = new $entityName;

		//数据库连接名，默认default
		$sourceName = $entity->getSourceName();
		//系统code对应数据库名
		$dbName = $entity->getSystemCode();
		//初始化数据库
		$dbSource = $this->setUpDb($sourceName, $dbName);

		$infos = $dbSource->findByIds($entity->getBindTable(), $ids);

		$objs = [];
		foreach($infos as $info){
			$objs[$info['id']] = self::toObject($entityName, $info);
		}
		return $objs;
	}

	public function save($entity)
    {
		//数据库连接名，默认default
		$sourceName = $entity->getSourceName();
		//系统code对应数据库名
		$dbName = $entity->getSystemCode();
		//初始化数据库
		$dbSource = $this->setUpDb($sourceName, $dbName);

		$entity->createId();

		$entity->ctime = $entity->utime = EzTimmer::now()->toString();

		$res = $dbSource->save($entity);

        $entityKey = $this->getEntityKey(get_class($entity), $entity->id);

		$this->cache->set($entityKey, serialize($entity));

        return $res;
	}

	public function update($entity)
    {
		//数据库连接名，默认default
		$sourceName = $entity->getSourceName();
		//系统code对应数据库名
		$dbName = $entity->getSystemCode();
		//初始化数据库
		$dbSource = $this->setUpDb($sourceName, $dbName);

        $entity->utime = EzTimmer::now()->toString();

		$res = $dbSource->update($entity);

        $entityKey = $this->getEntityKey(get_class($entity), $entity->id);

		$this->cache->set($entityKey, serialize($entity));
	}

	public function remove($entity)
    {
		//数据库连接名，默认default
		$sourceName = $entity->getSourceName();
		//系统code对应数据库名
		$dbName = $entity->getSystemCode();
		//初始化数据库
		$dbSource = $this->setUpDb($sourceName, $dbName);

		$res = $dbSource->delete($entity);

        $entityKey = $this->getEntityKey(get_class($entity), $entity->id);

		$this->cache->del($entityKey);
	}

    private function getEntityKey($entityName, $id)
    {
        $entity = new $entityName;
		return md5($entity->getSystemCode().'_'.$entityName.'_'.DataTransfer::toSimpleString($entity->getFields()).'_'.$id);
    }

	public function toObject($entityName, $info)
    {
		$entity = new $entityName;
		$fields = $entity->getFields();
		foreach($fields as $field){
            $entity->$field = array_key_exists(strtolower($field), $info) ? $info[strtolower($field)] : null;
		}

		return $entity;
	}
}
