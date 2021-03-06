<?php
class input extends library {
	public function __get($field)
		{
		switch ($field)
			{
			case 'get': return s('requestarray',$_GET);
			case 'post': return s('requestarray',$_POST);
			case 'request': return s('requestarray',$_REQUEST);
			case 'files': return s('requestfiles');
			default: return null;
			}
		}
	public function __set($field,$value)
		{
		switch ($field)
			{
			case 'get': $_GET = $value; break;
			case 'post': $_POST = $value; break;
			case 'request': $_REQUEST = $value; break;
			case 'files': $_FILES = $value; break;
			default: return null;
			}
		}
	public function __isset($field)
		{
		return (in_array($field,array('get','post','request','files')));
		}
}
class requestarray extends structure {
private $request;
	public function __construct(&$var)
		{
		$this->request = &$var;
		$this->set_array($this->request);
		}
	public function __get($field)
		{
		return $this->request[$field];
		}
	public function __set($field,$value)
		{
		$this->request[$field] = $value;
		}
	public function __call($field,$args)
		{
		switch (count($args))
			{
			case 0: return $this->$field;
			case 1: $method = $args[0]; return $this->$method($field);
			case 2: $method = $args[0]; return $this->$method($field) || $args[1];
			default: return null;
			}
		}
	public function __isset($field)
		{
		return isset($this->request[$field]);
		}
	public function __unset($field)
		{
		unset($this->request[$field]);
		}
	public function int($field)
		{
		return (int) $this->$field;
		}
	public function bool($field)
		{
		return (bool) $this->$field;
		}
	public function num($field)
		{
		return (float) $this->$field;
		}
	public function string($field)
		{
		return (string) $this->$field;
		}
	public function getarray($field)
		{
		return (array) $this->$field;
		}
}
class requestfiles extends structure {
private $files = array();
	public function __construct()
		{
		$this->process_uploads();
		$this->set_array($this->files);
		}
	private function process_uploads()
		{
		foreach ($_FILES as $name => $file)
			{
			if (is_array($file['name']))
				{
				$this->files[$name] = array();
				foreach ($file['name'] as $id => $filename)
					{
					if ($filename != '')
						{
						$this->files[$name][] = new file($file['tmp_name'][$id],$filename);
						}
					}
				}
			else
				{
				$this->files[$name] = new file($file['tmp_name'],$file['name']);
				}
			}
		}
	public function __get($field)
		{
		return $this->files[$field];
		}
	public function __isset($field)
		{
		return isset($this->files[$field]);
		}
	public function limit($field,$max = 1)
		{
		return array_slice($this->files[$field],0,$max);
		}
	public function uploads()
		{
		return $this->files;
		}
}
