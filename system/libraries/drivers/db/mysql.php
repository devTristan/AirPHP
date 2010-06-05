<?php
class db_mysql extends driver {
	public function connect($config)
		{
		return mysql_connect($config['server'],$config['username'],$config['password']);
		}
	public function pconnect($config)
		{
		return mysql_pconnect($config['server'],$config['username'],$config['password']);
		}
	public function set_database($link,$database)
		{
		return mysql_select_db($database,$link);
		}
	public function query($link,$sql)
		{
		return mysql_query($sql,$link);
		}
	public function unbuffered_query($link,$sql)
		{
		return mysql_unbuffered_query($sql,$link);
		}
	public function status($link)
		{
		return mysql_stat($link);
		}
	public function escape($link,$value,$ifstr = '')
		{
		if ($value === null) {return 'NULL';}
		if ($value === true) {return 'TRUE';}
		if ($value === false) {return 'FALSE';}
		if (is_numeric($value)) {return (string) $value;}
		return $ifstr.mysql_real_escape_string($value,$link).$ifstr;
		}
	public function affected_rows($link)
		{
		return mysql_affected_rows($link);
		}
	public function disconnect($link)
		{
		return mysql_close($link);
		}
	public function ping($link)
		{
		return mysql_ping($link);
		}
	public function fetch_assoc($resource)
		{
		return mysql_fetch_assoc($resource);
		}
	public function fetch_enum($resource)
		{
		return mysql_fetch_row($resource);
		}
	public function free_result($resource)
		{
		return mysql_free_result($resource);
		}
	public function build_create($link,$args)
		{
		$args = n('arr',$args)->addkeys(array(
			'db' => null,
			'table' => null,
			'rows' => null,
			'index' => null,
			'engine' => s('config')->db->default_engine
			));
		$sql = array();
		$sql[] = 'CREATE TABLE '.(($args['db'] === null) ? '' : $this->escape($link,$args['db'],'`').'.').$this->escape($link,$args['table'],'`').'(';
		$lines = array();
		foreach ($args['rows'] as $row)
			{
			$lines[] = $this->build_row($link,$row);
			}
		foreach ($args['index'] as $index)
			{
			$lines[] = $this->build_index($link,$index);
			}
		$lines = implode(",\n",$lines);
		$sql[] = $lines;
		$sql[] = ") ENGINE = ".$args['engine'];
		$sql = implode("\n",$sql);
		return $sql;
		}
	public function build_row($link,$args)
		{
		if (is_string($args)) {return $args;}
		$args = n('arr',$args)->addkeys(array(
			'name' => null,
			'type' => null,
			'length' => null,
			'null' => null,
			'unsigned' => null
			));
		return '`'.$args['name'].'` '.strtoupper($args['type']).
			'('.(($args['length']) ? $args['length'] : '').') '.
			(($args['unsigned']) ? 'UNSIGNED ': '').
			(($args['null']) ? 'NULL' : 'NOT NULL');
		}
	public function build_index($link,$args)
		{
		if (is_string($args)) {return $args;}
		return $this->driver('index_'.$args[0])->sql($args[1]);
		}
	public function build_select($link,$args)
		{
		$args = n('arr',$args)->addkeys(array(
			'fields' => '*',
			'db' => null,
			'table' => null,
			'where' => null,
			'group' => null,
			'having' => null,
			'order' => null,
			'limit' => null
			));
		$sql = array();
		$sql[] = 'SELECT '.$this->build_fields($link,$args['fields']);
		$sql[] = 'FROM '.$this->build_table($link,$args['db'],$args['table']);
		if ($args['where'] !== null) {$sql[] = 'WHERE '.$this->build_where($link,$args['where']);}
		if ($args['group'] !== null) {$sql[] = 'GROUP BY '.$this->build_order($link,$args['group']);}
		if ($args['having'] !== null) {$sql[] = 'HAVING '.$this->build_where($link,$args['having']);}
		if ($args['order'] !== null) {$sql[] = 'ORDER BY '.$this->build_order($link,$args['order']);}
		if ($args['limit'] !== null) {$sql[] = 'LIMIT '.$this->build_limit($link,$args['limit']);}
		$sql = implode(" \n",$sql);
		return $sql;
		}
	public function build_update($link,$args)
		{
		$args = n('arr',$args)->addkeys(array(
			'db' => null,
			'table' => null,
			'where' => null,
			'set' => null,
			'order' => null,
			'limit' => null
			));
		$sql = array();
		$sql[] = 'UPDATE '.$this->build_table($link,$args['db'],$args['table']);
		if ($args['set'] !== null) {$sql[] = 'SET '.$this->build_set($link,$args['set']);}
		if ($args['where'] !== null) {$sql[] = 'WHERE '.$this->build_where($link,$args['where']);}
		if ($args['order'] !== null) {$sql[] = 'ORDER BY '.$this->build_order($link,$args['order']);}
		if ($args['limit'] !== null) {$sql[] = 'LIMIT '.$this->build_limit($link,$args['limit']);}
		$sql = implode(" \n",$sql);
		return $sql;
		}
	public function build_set($link,$set,$value = null)
		{
		if (func_num_args() == 3 && is_string($set))
			{
			return str_replace('.','`.`',$this->escape($link,$set,'`')).' = '.$this->escape($link,$value,'"');
			}
		if (is_array($set))
			{
			$total = array();
			foreach ($set as $field => $value)
				{
				if (is_numeric($field))
					{
					$total[] = $this->build_set($link,$value);
					}
				else
					{
					$total[] = $this->build_set($link,$field,$value);
					}
				}
			$set = implode(', ',$total);
			}
		return $set;
		}
	public function build_fields($link,$fields)
		{
		if (is_string($fields))
			{
			if (strpos($fields,'`') === false && strpos($fields,'(') === false && strpos($fields,')') === false)
				{
				$fields = $this->escape($link,$fields,'`');
				if (strpos($fields,'.') !== false)
					{
					$fields = str_replace('.','`.`',$fields);
					}
				}
			}
		else if (is_array($fields))
			{
			$fieldarr = array();
			foreach ($fields as $field)
				{
				$fieldarr[] = $this->build_fields($link,$field);
				}
			$fields = implode(', ',$fieldarr);
			}
		return $fields;
		}
	public function build_table($link,$db,$table)
		{
		if (is_array($table))
			{
			$total = array();
			foreach ($table as $one)
				{
				$total[] = $this->build_table($link,$db,$one);
				}
			return implode(', ',$total);
			}
		else if (is_string($table) && strpos($table,',') !== false && strpos($table,'`') === false)
			{
			$tables = explode(',',$table);
			$tables = array_map('trim',$tables);
			return $this->build_table($link,$db,$tables);
			}
		else
			{
			$table = $this->escape($link,$table,'`');
			}
		if ($db) {$table = $this->escape($link,$db,'`').'.'.$table;}
		return $table;
		}
	public function build_order($link,$order)
		{
		if (is_array($order))
			{
			if (in_array(strtolower($order[count($order)-1]),array('asc','desc')))
				{
				$end = strtoupper(array_pop($order));
				}
			else
				{
				$end = 'ASC';
				}
			$order = $this->build_fields($link,$order);
			$order .= ' '.$end;
			}
		else if (is_string($order))
			{
			if (strpos($order,' ') === false && strpos($order,',') === false && strpos($order,'`') === false)
				{
				$order = $this->build_fields($link,$order).' ASC';
				}
			else
				{
				if (!str::endswith($order,'ASC') && !str::endswith($order,'DESC'))
					{
					$order .= ' ASC';
					}
				}
			}
		else
			{
			return '';
			}
		return $order;
		}
	public function build_limit($link,$limit)
		{
		if (is_numeric($limit))
			{
			$limit = '0, '.$limit;
			}
		else if (is_array($limit))
			{
			$limit = ((int) $limit[0]).', '.((int) $limit[1]);
			}
		return $limit;
		}
	public function build_where($link,$where,$value = null)
		{
		if (is_string($where))
			{
			if (func_num_args() == 1)
				{
				return $where;
				}
			else
				{
				$value = $this->escape($link,$value,'"');
				return '`'.$this->escape($link,$where).'` '.((is_array($value)) ? 'IN('.implode(',',$value).')' : '= '.$value);
				}
			}
		else
			{
			if (is_array($where))
				{
				$sql = array();
				$last_was_andor = false;
				foreach ($where as $where => $value)
					{
					$numeric = is_numeric($where);
					if (!$sql)
						{
						$sql[] = ($numeric) ? $this->build_subwhere($link,$value) : $this->build_subwhere($link,$where,$value);
						continue;
						}
					if ($numeric && is_string($value) && in_array(strtolower($value),array('or','and')))
						{
						if ($last_was_andor)
							{
							throw new MysqlInvalidAndOrException('You can\'t have two ANDs/ORs in a row');
							return false;
							}
						$sql[] = strtoupper($value);
						$last_was_andor = true;
						continue;
						}
					if ($last_was_andor)
						{
						$last_was_andor = false;
						}
					else
						{
						$sql[] = 'AND';
						}
					$sql[] = ($numeric) ? $this->build_subwhere($link,$value) : $this->build_subwhere($link,$where,$value);
					}
				$sql = implode(' ',$sql);
				return $sql;
				}
			else
				{
				return '';
				}
			}
		}
	private function build_subwhere($link,$where,$value = null)
		{
		$sql = $this->build_where($link,$where,$value);
		if (is_array($where)) {$sql = '('.$sql.')';}
		return $sql;
		}
}
class MysqlInvalidWhereException extends Exception {}
class db_mysql_index_unique extends driver {
	public function sql($args)
		{
		return 'UNIQUE KEY `'.implode(',',$args).'` (`'.implode('`, `',$args).'`)';
		}
}
class db_mysql_index_index extends driver {
	public function sql($args)
		{
		return 'KEY `'.implode(',',$args).'` (`'.implode('`, `',$args).'`)';
		}
}
class db_mysql_index_fulltext extends driver {
	public function sql($args)
		{
		return 'FULLTEXT (`'.implode('`, `',$args).'`)';
		}
}
