<?php
class BaseEntity{
	private $id;
	private $ctime;
	private $utime;
	public function getSystemCode(){
		Assert::exception('必须实现getSystemCode方法！(数据库名)');
	}

    public function getDao($code = '')
    {
        $code = get_class($this);
        $class = $code.'Dao';
        return new $class;
        if(empty($this->daos[$code]) || !$this->daos[$code] instanceof $class){
            $this->daos[$code] = new $class;
        }
        return $this->daos[$code];
    }

    //用来自定义实体和表绑定，默认为实体名小写
    public function getBindTable($table = '')
    {
        if(!empty($table))
            return $table;
        return strtolower(get_class($this));
    }

	//数据库连接名
	public function getSourceName(){
		return 'default';
	}

	public function getFields(){
		return array_keys(get_class_vars(get_class($this)));
	}

	public function __construct(){
	}

	public function createId(){
		$this->id = IdGenter::getInstance()->createId();
	}

	public function updateUtime(){
		$this->utime = EzTimmer::now()->toString();
	}

	public function __get($name){
		return $this->$name;
	}

	public function __set($property, $value){
		$this->$property = $value;
	}

	public function toString(){
		return json_encode($this);
	}

	public function toArray(){
        $res = [];
        foreach($this as $k => $v){
           $res[$k] = $v; 
        }
        return $res;
	}
}
