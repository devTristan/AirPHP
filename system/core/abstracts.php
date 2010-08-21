<?php
abstract class base {
	protected function hook($event,$method,$params = array())
		{
		s('event')->bind($this,$event,$method,$params);
		return $this;
		}
	protected function unhook($event,$method,$params = array())
		{
		s('event')->unbind($this,$event,$method,$params);
		return $this;
		}
}

abstract class library extends base {
private $_driver_cache = array();
	public function driver($name)
		{
		$thisclass = get_class($this);
		$driverclass = $thisclass.'_'.$name;
		if (isset(classmanager::$drivers[$driverclass])) {return classmanager::$drivers[$driverclass];}
		$dir = (isset($this->driverFolder)) ? $this->driverFolder : DIR_LIBRARIES.'drivers/';
		if (!class_exists($driverclass,false))
			{
			if (is_dir($dir))
				{
				$file = $dir.((isset($this->driverParent)) ? $this->driverParent : $thisclass).'/'.$name.'.php';
				if (file_exists($file))
					{
					require_once($file);
					}
				}
			}
		if (class_exists($driverclass,false))
			{
			s($driverclass)->driverFolder = $dir.((isset($this->driverParent)) ? $this->driverParent : $thisclass).'/';
			s($driverclass)->driverParent = $name;
			classmanager::$drivers[$driverclass] = s($driverclass);
			return s($driverclass);
			}
		else
			{
			classmanager::$drivers[$driverclass] = false;
			return false;
			}
		}
}
abstract class obj extends base {
private $_driver_cache = array();
	public function driver($name)
		{
		$thisclass = get_class($this);
		$driverclass = $thisclass.'_'.$name;
		if (isset(classmanager::$drivers[$driverclass])) {return classmanager::$drivers[$driverclass];}
		$dir = (isset($this->driverFolder)) ? $this->driverFolder : DIR_OBJECTS.'drivers/';
		if (!class_exists($driverclass,false))
			{
			if (is_dir($dir))
				{
				$file = $dir.((isset($this->driverParent)) ? $this->driverParent : $thisclass).'/'.$name.'.php';
				if (file_exists($file))
					{
					require_once($file);
					}
				}
			}
		if (class_exists($driverclass,false))
			{
			s($driverclass)->driverFolder = $dir.((isset($this->driverParent)) ? $this->driverParent : $thisclass).'/';
			s($driverclass)->driverParent = $name;
			classmanager::$drivers[$driverclass] = s($driverclass);
			return s($driverclass);
			}
		else
			{
			classmanager::$drivers[$driverclass] = false;
			return false;
			}
		}
}
class models extends base {
private $loaded = array();
	public function __get($model)
		{
		if (isset($this->loaded[$model])) {return $this->loaded[$model];}
		airphp_autoload('model');
		$fields = $index = $defaults = array();
		$identifier = null;
		$engine = s('config')->db->default_engine;
		include(DIR_MODELS.$model.'.php');
		$instance = s('model_'.$model);
		$this->loaded[$model] = $instance;
		$instance->fields = $fields;
		$instance->index = $index;
		$instance->defaults = $defaults;
		$instance->engine = $engine;
		$instance->identifier = $identifier;
		return $instance;
		}
}
abstract class driver extends library {
}
