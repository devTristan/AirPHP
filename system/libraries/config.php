<?php
class config extends classarray {
private $functions = array();
public $conf = array();
private $setup = false;
	public function __construct($file = 'config')
		{
		$this->file = $file;
		$this->set_array($this->conf);
		if (!defined('CONFIG_LOADED')) {define('CONFIG_LOADED',true);}
		}
	private function setup()
		{
		if ($this->setup === true) {return $this;}
		$this->setup = true;
		$this->extend('include',array($this,'_include'));
		$this->load(DIR_CONFIG.$this->file.EXT);
		return $this;
		}
	public function __get($var)
		{
		$this->setup();
		return isset($this->conf[$var]) ? ((is_object($this->conf[$var]) && get_class($this->conf[$var]) == 'config') ? $this->conf[$var]->setup()->conf : $this->conf[$var]) : array();
		}
	public function __isset($var)
		{
		$this->setup();
		return isset($this->conf[$var]);
		}
	public function __unset($var)
		{
		$this->setup();
		unset($this->conf[$var]);
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
		include($file);
		$this->conf = ($this->conf == array()) ? $config : array_merge_recursive($this->conf,$config);
		return $this;
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
