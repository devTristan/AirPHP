<?php
class console extends library {
private $error_types = array(
	'debug','warning','error','fatal error','forced'
	);
private $error_ids;
private $error_level = 1;
private $error_severity = array(
	E_ERROR			=> 'error',
	E_WARNING		=> 'warning',
	E_PARSE			=> 'error',
	E_NOTICE		=> 'debug',
	E_CORE_ERROR		=> 'error',
	E_CORE_WARNING		=> 'warning',
	E_COMPILE_ERROR		=> 'error',
	E_COMPILE_WARNING	=> 'warning',
	E_USER_ERROR		=> 'error',
	E_USER_WARNING		=> 'warning',
	E_USER_NOTICE		=> 'debug',
	E_STRICT		=> 'debug'
	);
private $levels = array(
	E_ERROR			=> 'Error',
	E_WARNING		=> 'Warning',
	E_PARSE			=> 'Parsing Error',
	E_NOTICE		=> 'Notice',
	E_CORE_ERROR		=> 'Core Error',
	E_CORE_WARNING		=> 'Core Warning',
	E_COMPILE_ERROR		=> 'Compile Error',
	E_COMPILE_WARNING	=> 'Compile Warning',
	E_USER_ERROR		=> 'User Error',
	E_USER_WARNING		=> 'User Warning',
	E_USER_NOTICE		=> 'User Notice',
	E_STRICT		=> 'Runtime Notice'
	);
	public function __construct($error_level = 2)
		{
		$this->error_level($error_level);
		array_unshift($this->error_types,null);
		unset($this->error_types[0]);
		$this->error_ids = array_flip($this->error_types);
		}
	public function error_level($level = null)
		{
		if ($level === null) {return $this->error_level;}
		else {$this->error_level = $level; return $this;}
		}
	public function log($msg,$type = 'system',$level = 'forced')
		{
		$level = $this->error_ids[$level];
		if ($level < $this->error_level) {return $this;}
		$msg = '['.date('h:i:sa').'] '.$msg."\n";
		$file = $type.'/'.date('M j, Y').'.log';
		$dirs = explode('/',$file);
		array_pop($dirs);
		$path = DIR_LOGS;
		foreach ($dirs as $dir)
			{
			$path .= $dir;
			if (!is_dir($path))
				{
				@mkdir($path,0770);
				}
			}
		file_put_contents(DIR_LOGS.$file,$msg,FILE_APPEND);
		return $this;
		}
	public function error($str = null, $file = null, $line = null, $type = 'error')
		{
		$message = $type;
		if ($file !== null)
			{
			if ($line !== null)
				{
				$message .= ' on line '.$line.' of '.$file;
				}
			else
				{
				$message .= ' in file '.$file;
				}
			}
		$message .= ': ';
		$message .= ($str !== null) ? $str : 'Unspecified';
		$this->log($message,'system',$this->error_ids[$type]);
		return $this;
		}
	public function fatal_error($str = null, $file = null, $line = null)
		{
		$this->error($str,$file,$line,'fatal error');
		die();
		}
	public function debug($msg,$type = 'system')
		{
		$this->log($msg,$type,'debug');
		return $this;
		}
	public function warning($str = null ,$file = null, $line = null)
		{
		$this->error($str,$file,$line,'warning');
		return $this;
		}
}
