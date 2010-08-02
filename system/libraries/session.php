<?php
class session extends structure {
private $data = array();
private $storage;
private $config;
private $deleted = false;
	public function __construct()
		{
		$this->set_array($this->data);
		$this->config = s('config')->session;
		$this->storage = s('cache', 'session', $this->config->drivers);
		if ($this->sid() !== true)
			{
			$this->data = $this->storage->get( $this->sid() );
			if ($this->data['useragent'] != $_SERVER['HTTP_USER_AGENT'])
				{
				$this->data = array();
				}
			}
		if (!$this->data)
			{
			$this->data = array();
			$this->data['sid'] = sha1(uniqid($_SERVER['REMOTE_ADDR'], true));
			s('input')->cookie->set($this->config->sid, $this->data['sid'], $this->config->timeout);
			$this->data['useragent'] = $_SERVER['HTTP_USER_AGENT'];
			}
		}
	private function sid()
		{
		return (isset(s('input')->cookie[$this->config->sid])) ? s('input')->cookie[$this->config->sid] : true;
		}
	public function __call($item, $default = null)
		{
		return (isset($this->data[$item])) ? $this->data[$item] : $default;
		}
	public function __destruct()
		{
		if (!$this->deleted && $this->data)
			{
			$this->storage->set($this->sid(), $this->data, $this->config->timeout);
			}
		}
	public function print_r($return = false)
		{
		return print_r($this->data, $return);
		}
	public function delete()
		{
		unset( $this->storage[$this->sid()] );
		unset( s('input')->cookie[$this->config->sid] );
		$this->data = array();
		$this->deleted = true;
		}
}
