<?php
class MySqlSyntax{
	
	private $useWhere = false;
	private $tableName;
	private $column = ' * ';
	private $where = ' where 1 = 1 ';
	private $groupBy;
	private $having;
	private $limit;

	//create
	private $fields;
	private $values;

	private $sql = '';

	public function table($tableName)
    {
		$this->tableName = $tableName;
		return $this;
	}

	public function column($columns = [])
    {
		$this->column = empty($columns) ? ' *' : ' '.trim(implode(',', $columns),',');
		return $this;
	}

	public function where($where)
    {
		if(!empty($where)){
			foreach($where as $columnName => $value){
				if(is_array($value)){
					$value = trim(implode(',', $value),',');
					$this->where .= ' and '.$columnName.' in ('.$value.')';
				}else{
					$this->where .= ' and '.$columnName.' = '.$value;
				}
			}
			$this->useWhere = true;
		}
		return $this;
	}

	public function buildSelect()
    {
		$this->sql .= 'select';
		$this->sql .= $this->column;
		$this->sql .= ' from '.$this->tableName;
		$this->sql .= $this->where;
		$this->sql .= $this->groupBy;
		$this->sql .= $this->having;
		$this->sql .= $this->limit;
		$this->checkSel();
		return $this->sql;
	}

	public function createField($fields)
    {
		$fields = array_map(function($v){
			return '`'.strtolower($v).'`';
		}, $fields);
		$this->fields = empty($fields) ? '' : trim(implode(',', $fields),',');
		return $this;
	}

	public function createValue($values)
    {
		$values = array_map(function($v){
			return "'".$v."'";
		}, $values);
		$this->values = empty($values) ? '' : trim(implode(',', $values),',');
		return $this;
	}

	public function buildCreate()
    {
		$this->sql .= 'insert into ';
		$this->sql .= $this->tableName;
		$this->sql .= ' ('.$this->fields.')';
		$this->sql .= ' values ('.$this->values.')';
		$this->checkCreate();
		return $this->sql;
	}

	public function updateColumn($arr)
    {
		$str = '';
		foreach($arr as $k=>$v){
			$str .= "`".$k."` = '".$v."',";
		}
		$this->column = trim($str,',');
		return $this;
	}

	public function buildUpdate()
    {
		$this->sql .= 'update ';
		$this->sql .= $this->tableName.' set ';
		$this->sql .= $this->column;
		$this->sql .= $this->where;
		$this->checkUpdate();
		return $this->sql;
	}

    public function buildDelete()
    {
        $this->sql .= 'delete from ';
        $this->sql .= $this->tableName;
        $this->sql .= $this->where;
        $this->checkDelete();
        return $this->sql;
    }

	//orm需要check
	public function checkSel()
    {
		if(empty($this->tableName)){
			Assert::exception('未选择表');
		}

		if($this->useWhere() && empty($this->where)){
			Assert::exception('where条件异常');
		}
        if (EnvSetup::isDev())
        {
            error_log(print_r($this->sql, true) . "\n", 3, '/tmp/allsql.log');
        }

	}

	public function checkCreate()
    {
        if (EnvSetup::isDev())
        {
            error_log(print_r($this->sql, true) . "\n", 3, '/tmp/allsql.log');
        }
	}

	public function checkUpdate()
    {
        if (EnvSetup::isDev())
        {
            error_log(print_r($this->sql, true) . "\n", 3, '/tmp/allsql.log');
        }
	}

    public function checkDelete()
    {
         if (EnvSetup::isDev())
        {
            error_log(print_r($this->sql, true) . "\n", 3, '/tmp/allsql.log');
        }
    }
	public function useWhere()
    {
		return $this->useWhere;
	}

}
