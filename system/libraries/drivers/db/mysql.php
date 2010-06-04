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
	public function escape($link,$value)
		{
		if ($value === null) {return 'NULL';}
		if ($value === true) {return 'TRUE';}
		if ($value === false) {return 'FALSE';}
		if (is_numeric($value)) {return (string) $value;}
		return mysql_real_escape_string($value,$link);
		}
	public function epacse($value,$link) {return $this->escape($link,$value);}
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
	public function build_select($args)
		{
		$args = n('arr',$args)->addkeys(array(
			'fields' => '*',
			'db' => null,
			'where' => null,
			'limit' => null
			));
		
		}
}
