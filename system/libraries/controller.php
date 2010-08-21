<?php
abstract class controller extends base {
private $_api_args = false;
private $_api_formats = array('json', 'xml', 'yaml');
private $_api_format, $_api_callback;
	public function __get($classname)
		{
		return s($classname);
		}
	protected function api()
		{
		if (!$this->config->api->enabled) {die();}
		
		$this->_api_args = func_get_args();
		$this->_api_format = in_array($this->input->get->format(''), $this->_api_formats)
			? $this->input->get->format
			: $this->config->api->default_format;
		$this->_api_callback = $this->input->get->callback('');
		$params = $this->_api_args;
		$callbacks = false;
		$allowed = false;
		foreach ($params as &$param)
			{
			if ($param == 'callbacks')
				{
				$callbacks = true;
				if ($allowed) {break;}
				}
			else if ($param == $this->_api_format)
				{
				$allowed = true;
				if ($callbacks) {break;}
				}
			}
		if (!$callbacks && $this->_api_callback)
			{
			$allowed = false;
			}
		if (!$allowed)
			{
			$this->api_error('Not Allowed');
			}
		
		$this->hook('controller_after', '_api_callback');
		}
	public function _api_callback($data)
		{
		$this->_api_message($data);
		}
	private function _api_message($data)
		{
		switch ($this->_api_format)
			{
			case 'json': $data = json_encode($data); break;
			case 'xml': /*TODO: xml encode;*/ break;
			case 'yaml': $data = sfYaml::dump($data); break;
			}
		if ($this->_api_callback)
			{
			$data = $this->_api_callback.'('.(($this->_api_format == 'json') ? $data : json_encode($data)).');';
			$this->_api_format = 'js';
			}
		$this->output->header('Content-Type', $this->config->mimes[$this->_api_format]);
		echo $data;
		}
	protected function api_error($msg)
		{
		$this->_api_message(array('error' => $msg));
		die();
		}
}
