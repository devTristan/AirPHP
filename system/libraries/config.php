<?php
class config extends structure {
private $functions = array();
public $conf = array();
private $setup = false;
private $modified = false;
private $file;
	public function __construct($file = 'config')
		{
		$this->file = $file;
		$this->set_array($this->conf);
		if (!defined('CONFIG_LOADED')) {define('CONFIG_LOADED',true);}
		}
	public function ___destruct()
		{
		if ($this->modified)
			{
			$this->save();
			}
		}
	private function setup()
		{
		if ($this->setup === true) {return $this;}
		$this->setup = true;
		$this->extend('include',array($this,'_include'));
		$this->load(DIR_CONFIG.$this->file);
		return $this;
		}
	public function __get($var)
		{
		$this->setup();
		if (isset($this->conf[$var]))
			{
			return $this->conf[$var];
			}
		else
			{
			if (file_exists(DIR_CONFIG.$var.'.php'))
				{
				return s('config',$var);
				}
			else
				{
				return array();
				}
			}
		}
	public function __set($var,$value)
		{
		$this->modifed = true;
		$this->conf[$var] = $value;
		}
	public function __isset($var)
		{
		$this->setup();
		if (isset($this->conf[$var]))
			{
			return true;
			}
		else
			{
			return file_exists(DIR_CONFIG.$var.'.php');
			}
		}
	public function __unset($var)
		{
		$this->modifed = true;
		unset($this->conf[$var]);
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
	public function __toString()
		{
		$this->setup();
		$str = '';
		foreach ($this->conf as $key => $value)
			{
			$str .= $key.': '.print_r($value,true)."<br/>\n";
			}
		return $str;
		}
	public function _include($file)
		{
		$this->load($file);
		}
	public function load($file)
		{
		include($file.'.php');
		$this->conf = ($this->conf == array()) ? $config : array_merge_recursive($this->conf,$config);
		return $this;
		}
	public function save($file = null)
		{
		if (!func_num_args())
			{
			$file = $this->file;
			}
		$file .= '.php';
		}
	public function extend($name,$function)
		{
		$this->functions[$name] = $function;
		return $this;
		}
	private function run_function($function,$args)
		{
		if (isset($this->functions[$function]))
			{
			return array(true,call_user_func_array($this->functions[$function],$args));
			}
		return array(false,'No such config function: '.$function);
		}
}
