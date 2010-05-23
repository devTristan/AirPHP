<?php
class output extends library {
private $headers = array();
	public function start()
		{
		ob_start();
		return $this;
		}
	public function end()
		{
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
				header($prefix.' '.$value,true,$code);
				}
			else
				{
				header($field.': '.$value,true);
				}
			}
		ob_end_flush();
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
}
