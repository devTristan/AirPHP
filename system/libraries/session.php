<?php
class session extends structure {
private $data = array();
private $original_data = array();
private $storage;
private $config;
private $deleted = false;
public $sid;
	public function __construct()
		{
		$this->set_array($this->data);
		$this->config = s('config')->session;
		
		$drivers = $this->config->drivers;
		array_unshift($drivers, 'array');
		$this->storage = s('cache', 'session', $this->config->drivers);
		
		if (isset( s('input')->cookie[$this->config->sid] ))
			{
			$this->sid = s('input')->cookie[$this->config->sid];
			$this->data = $this->original_data = $this->storage->get( $this->sid );
			if ($this->data['useragent'] != $_SERVER['HTTP_USER_AGENT'] || $this->data['ip'] != $_SERVER['REMOTE_ADDR'])
				{
				$this->data = $this->original_data = array();
				}
			}
		
		if (!$this->data)
			{
			$this->sid = sha1(uniqid($_SERVER['REMOTE_ADDR'], true));
			$this->data = array();
			s('input')->cookie->set($this->config->sid, $this->sid, $this->config->timeout);
			$this->data['useragent'] = $_SERVER['HTTP_USER_AGENT'];
			$this->data['ip'] = $_SERVER['REMOTE_ADDR'];
			}
		}
	public function __call($item, $default = null)
		{
		return (isset($this->data[$item])) ? $this->data[$item] : $default;
		}
	public function __destruct()
		{
		if (!$this->deleted && $this->data)
			{
			$this->storage->set($this->sid, $this->data, $this->config->timeout);
			}
		}
	public function print_r($return = false)
		{
		return print_r($this->data, $return);
		}
	public function delete()
		{
		unset( $this->storage[$this->sid] );
		unset( s('input')->cookie[$this->config->sid] );
		$this->data = array();
		$this->deleted = true;
		}
}
