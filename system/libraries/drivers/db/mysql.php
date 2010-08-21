<?php
class db_mysql extends driver {
public $name = 'MySQL';
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
	public function version($link)
		{
		$ver = $this->fetch_enum($this->query($link,'SELECT VERSION()'));
		$ver = $ver[0];
		return $ver;
		}
	public function status($link)
		{
		$stat = mysql_stat($link);
		$stat = explode('  ',$stat);
		$newstat = array();
		$newstat['Version'] = $this->version($link);
		foreach ($stat as $line)
			{
			list($key,$value) = explode(': ',$line);
			$newstat[$key] = $value;
			}
		$stat = $newstat;
		$stat['Uptime'] = number::timespan($newstat['Uptime']);
		foreach ($stat as $key => &$value)
			{
			if (is_numeric($value))
				{
				$value = number_format($value,3);
				if (strpos($value,'.') !== false)
					{
					while (substr($value,-1) == '0')
						{
						$value = substr($value,0,-1);
						}
					if (substr($value,-1) == '.')
						{
						$value = substr($value,0,-1);
						}
					}
				}
			$value = $key.': '.$value;
			}
		$stat = implode("\n",$stat);
		return $stat;
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
	public function num_rows($link, $result)
		{
		return mysql_num_rows($result);
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
		return (is_resource($resource)) ? mysql_fetch_assoc($resource) : false;
		}
	public function fetch_enum($resource)
		{
		return (is_resource($resource)) ? mysql_fetch_row($resource) : false;
		}
	public function free_result($resource)
		{
		return mysql_free_result($resource);
		}
	public function last_insert_id($link)
		{
		$result = $this->query($link, 'SELECT LAST_INSERT_ID()');
		$row = $this->fetch_enum($result);
		return $row[0];
		}
	public function error($link)
		{
		$error = mysql_error($link);
		$list = explode(' ',$error);
		$list = array_reverse($list);
		if ((int) $list[0] && $list[1] == 'line' && $list[2] == 'at')
			{
			$line = (int) $list[0];
			$parts = array_reverse(explode("'",$error));
			$highlight = $parts[1];
			}
		else
			{
			$line = null;
			$highlight = null;
			}
		return array(
			'string' => $error,
			'line' => $line,
			'highlight' => $highlight
			);
		}
	public function build_create($link,$args)
		{
		$args = n('arr',$args)->addkeys(array(
			'db' => null,
			'table' => null,
			'rows' => null,
			'index' => null,
			'engine' => s('config')->db->default_engine,
			'temporary' => false
			));
		$sql = array();
		$sql[] = 'CREATE TABLE '.(($args['temporary']) ? 'TEMPORARY ' : '')
			.(($args['db'] === null) ? '' : $this->escape($link,$args['db'],'`').'.')
			.$this->escape($link,$args['table'],'`').'(';
		$lines = array();
		foreach ($args['rows'] as $row)
			{
			$lines[] = $this->build_row($link, $row);
			}
		foreach ($args['index'] as $index)
			{
			$lines[] = $this->build_index($link, $index);
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
			'args' => '',
			'null' => null,
			'unsigned' => null,
			'auto_increment' => null
			))->toArray();
		$sql = array();
		if ($args['args'])
			{
			if (!is_array($args['args']))
				{
				$args['args'] = array($args['args']);
				}
			foreach ($args['args'] as $arg_id => $arg)
				{
				$args['args'][$arg_id] = $this->escape($link, $arg, '"');
				}
			$args['args'] = '('.implode(', ', $args['args']).')';
			}
		$sql[] = '`'.$args['name'].'` '.strtoupper($args['type']).$args['args'];
		if ($args['unsigned']) {$sql[] = 'UNSIGNED';}
		$sql[] = ($args['null']) ? 'NULL' : 'NOT NULL';
		if (isset($args['default'])) {$sql[] = 'DEFAULT '.$this->escape($link, $args['default'], '"');}
		if ($args['auto_increment']) {$sql[] = 'AUTO_INCREMENT';}
		$sql = implode(' ',$sql);
		return $sql;
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
			'limit' => null,
			'options' => null
			));
		$sql = array();
		$firstline = 'SELECT ';
		if ($args['options']) {$firstline .= implode(' ',array_map($args['options'],'strtoupper')).' ';}
		$firstline .= $this->build_fields($link,$args['fields']);
		$sql[] = $firstline;
		$sql[] = 'FROM '.$this->build_table($link,$args['db'],$args['table']);
		if ($args['where']) {$sql[] = 'WHERE '.$this->build_where($link,$args['where']);}
		if ($args['group']) {$sql[] = 'GROUP BY '.$this->build_order($link,$args['group']);}
		if ($args['having']) {$sql[] = 'HAVING '.$this->build_where($link,$args['having']);}
		if ($args['order']) {$sql[] = 'ORDER BY '.$this->build_order($link,$args['order']);}
		if ($args['limit']) {$sql[] = 'LIMIT '.$this->build_limit($link,$args['limit']);}
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
			'limit' => null,
			'options' => null
			));
		$sql = array();
		$firstline = 'UPDATE ';
		if ($args['options']) {$firstline .= implode(' ',array_map($args['options'],'strtoupper')).' ';}
		$firstline .= $this->build_table($link,$args['db'],$args['table']);
		$sql[] = $firstline;
		if ($args['set'] !== null) {$sql[] = 'SET '.$this->build_set($link,$args['set']);}
		if ($args['where'] !== null) {$sql[] = 'WHERE '.$this->build_where($link,$args['where']);}
		if ($args['order'] !== null) {$sql[] = 'ORDER BY '.$this->build_order($link,$args['order']);}
		if ($args['limit'] !== null) {$sql[] = 'LIMIT '.$this->build_updatelimit($link,$args['limit']);}
		$sql = implode(" \n",$sql);
		return $sql;
		}
	public function build_delete($link,$args)
		{
		$args = n('arr',$args)->addkeys(array(
			'db' => null,
			'table' => null,
			'where' => null,
			'set' => null,
			'order' => null,
			'limit' => null,
			'options' => null
			));
		$sql = array();
		$firstline = 'DELETE ';
		if ($args['options']) {$firstline .= implode(' ',array_map($args['options'],'strtoupper')).' ';}
		$firstline .= $this->build_table($link,$args['db'],$args['table']);
		$sql[] = $firstline;
		if ($args['set'] !== null) {$sql[] = 'SET '.$this->build_set($link,$args['set']);}
		if ($args['where'] !== null) {$sql[] = 'WHERE '.$this->build_where($link,$args['where']);}
		if ($args['order'] !== null) {$sql[] = 'ORDER BY '.$this->build_order($link,$args['order']);}
		if ($args['limit'] !== null) {$sql[] = 'LIMIT '.$this->build_limit($link,$args['limit']);}
		$sql = implode(" \n",$sql);
		return $sql;
		}
	public function build_insert($link, $args)
		{
		$args = n('arr',$args)->addkeys(array(
			'db' => null,
			'table' => null,
			'data' => null,
			'options' => null
			));
		if (!$args['data']) {return;}
		$sql = array();
		$firstline = 'INSERT ';
		if ($args['options']) {$firstline .= implode(' ', array_map($args['options'],'strtoupper')).' ';}
		$firstline .= 'INTO '.$this->build_table($link, $args['db'], $args['table']);
		$sql[] = $firstline;
		
		$keys = array_keys(isset($args['data'][0]) ? $args['data'][0] : $args['data']);
		$sql[] = '(' . $this->build_fields($link, $keys) . ')';
		$sql[] = 'VALUES';
		
		if (!isset($args['data'][0]))
			{
			$data = array($args['data']);
			}
		$rows = array();
		foreach ($data as $row)
			{
			$row = array_values($row);
			foreach ($row as &$value)
				{
				$value = $this->escape($link, $value, '"');
				}
			$rows[] = '(' . implode(', ', $row) . ')';
			}
		$rows = implode(", \n", $rows);
		
		$sql[] = $rows;
		
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
	public function build_fields($link, $fields)
		{
		if (is_string($fields))
			{
			if ($fields != '*' && strpos($fields,'`') === false && strpos($fields,'(') === false && strpos($fields,')') === false)
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
			foreach ($fields as $field => $alias)
				{
				if (is_numeric($field))
					{
					$fieldarr[] = $this->build_fields($link,$alias);
					}
				else
					{
					$fieldarr[] = $this->build_fields($link,$this->build_fields($link,$field).' as '.$this->escape($link,$alias,'`'));
					}
				}
			$fields = implode(", ",$fieldarr);
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
	public function build_updatelimit($link,$limit)
		{
		return (string) ((int) $limit);
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
class db_mysql_index_primary extends driver {
	public function sql($args)
		{
		return 'PRIMARY KEY (`'.implode('`, `',$args).'`)';
		}
}
class db_mysql_index_fulltext extends driver {
	public function sql($args)
		{
		return 'FULLTEXT (`'.implode('`, `',$args).'`)';
		}
}
