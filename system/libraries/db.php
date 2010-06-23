<?php
class db extends library {
private $connection;
private $config;
public $querylist = array();
	public function __construct($config = null)
		{
		if ($config == null) {$config = s('config')->db;}
		$this->config = $config;
		}
	private function worker()
		{
		return $this->driver($this->config['type']);
		}
	public function connection()
		{
		if (!$this->connected())
			{
			$method = (($this->config['persistent']) ? 'p' : '').'connect';
			$this->connection = $this->worker()->$method($this->config);
			if ($this->config['database'])
				{
				$this->worker()->set_database($this->connection,$this->config['database']);
				}
			}
		return $this->connection;
		}
	public function connected()
		{
		return isset($this->connection);
		}
	public function query($sql, $multi = false)
		{
		if ($multi === false && str::contains($sql,';'))
			{
			show_error('Individual queries cannot contain colons<h2>SQL Query</h2><pre>'.$sql.'</pre>','db');
			}
		$connection = $this->connection();
		$worker = $this->worker();
		$start = microtime(true);
		$result = $worker->query($connection,$sql);
		$end = microtime(true);
		$span = $end-$start;
		$this->querylist[] = array($span,$sql);
		if ($result === false)
			{
			$error = $this->worker()->error($this->connection());
			if ($error['line'] !== null)
				{
				$sql = explode("\n",$sql);
				$sql[$error['line']-1] = str_replace($error['highlight'],'<span class="highlight">'.$error['highlight'].'</span>',$sql[$error['line']-1]);
				$sql = implode("\n",$sql);
				}
			show_error($error['string'].'<h2>SQL Query</h2><pre>'.$sql.'</pre>','db');
			}
		return $result;
		}
	public function unbuffered_query($sql)
		{
		return $this->worker()->unbuffered_query($this->connection,$sql);
		}
	public function status()
		{
		return $this->worker()->status($this->connection());
		}
	public function escape($value,$ifstr = '')
		{
		if (is_array($value))
			{
			foreach ($value as &$subvalue)
				{
				$subvalue = $this->escape($subvalue,$ifstr);
				}
			return $value;
			}
		return $this->worker()->escape($this->connection(),$value,$ifstr);
		}
	public function affected_rows()
		{
		return $this->worker()->affected_rows($this->connection());
		}
	public function disconnect()
		{
		if ($this->connected() && !$this->config['persistent'])
			{
			$this->worker()->disconnect($this->connection());
			unset($this->connection);
			}
		}
	public function ping()
		{
		$this->worker()->ping($this->connection());
		}
	public function fetch($result)
		{
		return $this->fetch_assoc($result);
		}
	public function fetch_all($result)
		{
		$rows = array();
		while ($row = $this->fetch_assoc($result))
			{
			$rows[] = $row;
			}
		return $rows;
		}
	public function __call($method,$args)
		{
		if (!in_array($method,array('fetch_enum','fetch_assoc','free_result')))
			{
			array_unshift($args,$this->connection());
			}
		return call_user_func_array(array($this->worker(),$method),$args);
		}
	public function __destruct()
		{
		$this->disconnect();
		}
}
