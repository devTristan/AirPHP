<?php
class cache extends library {
private $drivers = array();
private $prefix;
	public function __construct($dummy1,$dummy2)
		{
		$args = func_get_args();
		$this->prefix = 'airphp_'.array_shift($args).'_';
		if (count($args) == 1 && is_array($args[0])) {$args = $args[0];}
		$this->drivers = $args;
		}
	public function __get($item)
		{
		$item = $this->prefix.$item;
		$item = sha1($item);
		$lineup = array();
		foreach ($this->drivers as $driver)
			{
			$value = $this->driver($driver)->get($item);
			if ($value !== false)
				{
				foreach ($lineup as $driver2)
					{
					$this->driver($driver2)->set($item,$value);
					}
				return $value;
				}
			$lineup[] = $driver;
			}
		return false;
		}
	public function __set($item,$value)
		{
		$this->set($item,$value);
		}
	public function set($item,$value,$time = -1)
		{
		$item = $this->prefix.$item;
		$item = sha1($item);
		foreach ($this->drivers as $driver)
			{
			if (!$this->driver($driver)->set($item,$value,$time))
				{
				break;
				}
			}
		}
	public function __isset($item)
		{
		$item = $this->prefix.$item;
		$item = sha1($item);
		foreach ($this->drivers as $driver)
			{
			if ($this->driver($driver)->exists($item))
				{
				return true;
				}
			}
		return false;
		}
	public function __unset($item)
		{
		$item = $this->prefix.$item;
		$item = sha1($item);
		foreach ($this->drivers as $driver)
			{
			$this->driver($driver)->remove($item);
			}
		}
	public function clear()
		{
		foreach ($this->drivers as $driver)
			{
			$this->driver($driver)->clear();
			}
		}
}
