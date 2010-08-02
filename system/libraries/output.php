<?php
class output extends library {
private $headers = array();
private $cookies = array();
private $cachetime = 0;
private $rawheaders;
	public function start()
		{
		ob_start();
		return $this;
		}
	public function end()
		{
		$this->send_headers();
		if ($this->cachetime)
			{
			$parts = explode('?',$_SERVER['REQUEST_URI']);
			$file = DIR_CACHE.'output_'.sha1(array_shift($parts));
			file_put_contents($file,
				(time()+$this->cachetime)."\n".
				json_encode($this->rawheaders)."\n".
				ob_get_contents()
				);
			}
		@ob_end_flush();
		return $this;
		}
	public function send_headers()
		{
		$this->rawheaders = array('status' => false, 'normal' => array());
		foreach ($this->headers as $field => $value)
			{
			if ($field == 'Status')
				{
				$server_protocol = (isset($_SERVER['SERVER_PROTOCOL'])) ? $_SERVER['SERVER_PROTOCOL'] : false;
				$prefix = (substr(php_sapi_name(), 0, 3) == 'cgi') ? 'Status:' : (($server_protocol == 'HTTP/1.0') ? 'HTTP/1.0' : 'HTTP/1.1');
				if (is_numeric($value))
					{
					$code = $value;
					}
				else
					{
					$code = (int) substr($value,0,3);
					}
				$this->rawheaders['status'] = array($prefix.' '.$value,$code);
				header($prefix.' '.$value,true,$code);
				}
			else
				{
				$this->rawheaders['normal'][] = $field.': '.$value;
				header($field.': '.$value,true);
				}
			}
		$this->headers = array();
		foreach ($this->cookies as $cookie)
			{
			call_user_func_array('setcookie', $cookie);
			}
		$this->cookies = array();
		return $this;
		}
	public function flush()
		{
		$this->send_headers();
		flush();
		ob_flush();
		return $this;
		}
	public function cache($cachetime)
		{
		$this->cachetime = $cachetime;
		return $this;
		}
	public function client_cache($cachetime)
		{
		$cachetime = (int) $cachetime;
		$this->header('Cache-Control','public, max-age='.$cachetime);
		return $this;
		}
	public function header($field,$value = null)
		{
		if ($value === null)
			{
			$value = $field;
			$field = 'Status';
			}
		$this->headers[$field] = $value;
		return $this;
		}
	public function set_cookie($item, $value, $timeout = null, $path = null, $domain = null, $secure = null, $httponly = null)
		{
		if ($timeout === null) {$timeout = s('config')->cookies->default_timeout;}
		if ($path === null) {$path = s('config')->cookies->path;}
		if ($domain === null) {$domain = s('config')->cookies->domain;}
		if ($secure === null) {$secure = s('config')->cookies->secure;}
		if ($httponly === null) {$httponly = s('config')->cookies->httponly;}
		$item = s('config')->cookies->prefix.$item;
		$this->cookies[$item] = array($item, $value, $timeout, $path, $domain, $secure, $httponly);
		}
	public function redirect($url, $permanent = false)
		{
		if ($permanent) {$this->header(301);}
		$this->header('Location', $url);
		}
}
