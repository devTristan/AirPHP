<?php
class db_mysql extends driver {
	public function connect($config)
		{
		return mysql_connect($config['server'],$config['username'],['password']);
		}
	public function pconnect($config)
		{
		return mysql_pconnect($config['server'],$config['username'],['password']);
		}
	public function set_database($database)
		{
		return mysql_select_db($database,$this->connection());
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
	public function escape($link,$string)
		{
		return mysql_real_escape_string($string,$link);
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
}
