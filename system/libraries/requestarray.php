<?php
class requestarray extends classarray {
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
