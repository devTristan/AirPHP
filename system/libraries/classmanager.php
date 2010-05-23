<?php
class classmanager extends library {
static private $instances = array();
static private $cache_compatibility_type = array();
static public $drivers = array();
	static public function single($args)
		{
		$id = sha1(serialize($args));
		if (!isset(self::$instances[$id]))
			{
			self::$instances[$id] = self::new_instance($args);
			}
		return self::$instances[$id];
		}
	static public function new_instance($args)
		{
		$class = array_shift($args);
		if ($class == 'ReflectionClass')
			{
			$id = sha1(serialize(array('ReflectionClass',$args[0])));
			if (!isset(self::$instances[$id]))
				{
				self::$instances[$id] = new ReflectionClass($args[0]);
				}
			return self::$instances[$id];
			}
		switch (count($args))
			{
			case 0: return new $class;
			case 1: return new $class($args[0]);
			default: return self::single(array('ReflectionClass',$class))->newInstanceArgs($args);
			}
		}
	static public function class_type($class)
		{
		if (!isset(self::$cache_compatibility_type[$class]))
			{
			if (!defined('CONFIG_LOADED')) {return '';}
			foreach (s('config')->classtypes as $type => $data)
				{
				if (substr($class,0,strlen($data['prefix'])) == $data['prefix'])
					{
					$compat = (isset($data['is_compatibility']) && $data['is_compatibility']) ? true : false;
					self::$cache_compatibility_type[$class] = array($data['prefix'],$type,$compat);
					break;
					}
				}
			}
		return (isset(self::$cache_compatibility_type[$class])) ? self::$cache_compatibility_type[$class][1] : '';
		}
	static public function class_prefix($class)
		{
		if (isset(self::$cache_compatibility_type[$class]))
			{
			return self::$cache_compatibility_type[$class][0];
			}
		else
			{
			self::class_type($class);
			return (isset(self::$cache_compatibility_type[$class])) ? self::$cache_compatibility_type[$class][0] : '';
			}
		}
	static public function class_is_compatibility($class)
		{
		if (isset(self::$cache_compatibility_type[$class]))
			{
			return self::$cache_compatibility_type[$class][2];
			}
		else
			{
			self::class_type($class);
			return (isset(self::$cache_compatibility_type[$class])) ? self::$cache_compatibility_type[$class][2] : false;
			}
		}
	static public function set($name,$value)
		{
		self::$instances[sha1(serialize(array($name)))] = $value;
		}
	private function __construct() {}
	private function __destruct() {}
	private function __clone() {}
	private function __wakeup() {}
}
