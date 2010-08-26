<?php
class cache extends library implements ArrayAccess {
private $drivers = array();
private $prefix;
private static $methodcache = array();
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
	public function get($item) { return $this->__get($item); }
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
			$this->driver($driver)->set($item,$value,$time);
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
	public function offsetExists($offset)
		{
		return $this->__isset($offset);
		}
	public function offsetGet($offset)
		{
		return $this->__get($offset);
		}
	public function offsetSet($offset,$value)
		{
		$this->__set($offset,$value);
		}
	public function offsetUnset($offset)
		{
		$this->__unset($offset);
		}
	public function clear($type = null)
		{
		if ($type === null)
			{
			foreach ($this->drivers as $driver)
				{
				$this->driver($driver)->clear();
				}
			}
		else
			{
			$this->driver($type)->clear();
			}
		}
	public static function method($return_value = null)
		{
		$backtrace = debug_backtrace();
		$key = serialize( array($backtrace[1]['class'], $backtrace[1]['function'], $backtrace[1]['args']) );
		
		if (!isset( self::$methodcache[$key] ))
			{
			//step one - isset
			$args = func_get_args();
			if ( is_int($args[count($args)-1]) )
				{
				$timeout = array_pop($args);
				}
			else
				{
				$timeout = -1;
				}
			self::$methodcache[$key] = array($args ,$timeout);
			$store = s('cache', 'methodcache', self::$methodcache[$key][0]);
			return isset( $store->$key );
			}
		else
			{
			$store = s('cache', 'methodcache', self::$methodcache[$key][0]);
			if (!func_num_args())
				{
				//step two - get
				return $store->$key;
				}
			else
				{
				//step three - set
				$store->set($key, $return_value, self::$methodcache[$key][1]);
				unset(self::$methodcache[$key]);
				}
			}
		}
}
