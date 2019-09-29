<?php
/*
*   实现数据库的一系列操作
*/
class DAL{
	private $entityFactory;
	private $db;

	public function __construct($dbName, $source)
    {
		$this->entityFactory = new EntityFactory();
        $this->db = $this->getDb($source)->selectDb($dbName);
	}

    public function useMaster()
    {
        $this->db = getDb('default');
        return $this;    
    }

	public function getDb($source)
    {
		if(empty(Loadder::getDb($source))){
			Loadder::setDb($source, new MySql($source));
		}
		return Loadder::getDb($source);
	}

	public static function get($dbName = '', $source = 'default')
    {
		return new self($dbName, $source);
	}

    public function selectDb($dbName)
    {
        if(!empty($dbName))
            call_user_func_array([$this->db, __FUNCTION__], func_get_args());
        return $this;     
    }

    public function query($sql, $binds = [])
    {
        $sql = $this->bindSqlParam($sql, $binds);
        return call_user_func_array([$this->db, __FUNCTION__], [$sql]); 
    }

    public function queryValue($sql, $binds = [], $column = '')
    {
        $sql = $this->bindSqlParam($sql, $binds);
        return call_user_func_array([$this->db, __FUNCTION__], [$sql, $column]); 
    }

    public function bindSqlParam($sql, $binds = [])
    {
        if(empty($binds))
            return $sql;
        foreach($binds as $k => $v)
        {
            if(is_array($v))
            {
                $tmp = '';
                foreach($v as $vi)
                {
                    $tmp .= "'".$vi."',";
                }
                $tmp = trim($tmp, ',');
            }
            else
            {
                $tmp = "'".$v."'";
            }
            //$k = ' '.$k.' ';
            //$tmp = ' '.$tmp.' ';
            $sql = str_replace($k, $tmp, $sql);
        }
        return $sql;
    }

	public static function update(BaseEntity $entity)
    {
		$factory = new EntityFactory();
		return $factory->update($entity);
	}

	public static function save(BaseEntity $entity)
    {
		$factory = new EntityFactory();
		return $factory->save($entity);
	}

    public static function remove(BaseEntity $entity)
    {
       $factory = new EntityFactory; 
       return $factory->remove($entity);
    }

	public function find($entityName, $id, $onlyDb = false)
    {
        return $this->entityFactory->find($entityName, $id, $onlyDb);
	}

    public function findList($entityName, $ids)
    {
        return $this->entityFactory->findList($entityName, $ids);
    }

}
