<?php
class arr extends structure {
private $data;
	public function __construct($data = array())
		{
		call_user_func_array(array($this,'raw'),func_get_args());
		$this->set_array($this->data);
		}
	public function raw($data = null)
		{
		if (func_num_args() == 0) {return $this->data;}
		if (!is_array($data) || func_num_args() > 1)
			{
			$data = func_get_args();
			}
		$this->data = $data;
		return $this;
		}
	public function addkeys($keys)
		{
		if (!is_array($keys) || func_num_args() > 1)
			{
			$keys = func_get_args();
			}
		$i = 0;
		foreach ($keys as $key => $default)
			{
			if (is_numeric($key))
				{
				if (isset($this->data[$i]))
					{
					$this->data[$default] = $this->data[$i];
					unset($this->data[$i]);
					}
				else
					{
					break;
					}
				}
			else
				{
				if (!isset($this->data[$key]))
					{
					$this->data[$key] = $default;
					}
				}
			}
		return $this;
		}
	public function set($field,$value)
		{
		$this->data[$field] = $value;
		return $this;
		}
	public function get($field)
		{
		return $this->data[$field];
		}
	public function toArray()
		{
		return $this->data;
		}
	public function print_r($return = false)
		{
		if ($return)
			{
			return print_r($this->data,true);
			}
		print_r($this->data);
		}
}
