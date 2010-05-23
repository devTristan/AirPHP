<?php
class file extends library {
private $_location;
private $name;
private $folder;
private $extension;
private $mimetype;
private $group;
private $mode;
private $owner;
	public function __construct($location = null,$name = null)
		{
		if ($location === null)
			{
			$location = tempnam(sys_get_temp_dir());
			}
		$this->_location = $location;
		if ($name !== null)
			{
			$this->name = $name;
			}
		}
	public function __get($var)
		{
		return $this->$var();
		}
	public function __set($var,$value)
		{
		$this->$var($value);
		}
	public function __call($method,$args)
		{
		if (substr($method,0,4) == 'get_' || substr($method,0,4) == 'set_') {return null;}
		if ($args)
			{
			if (!method_exists($this,'set_'.$method))
				{
				echo '<pre>'.print_r(debug_backtrace(),true).'</pre>';
				trigger_error('You can\'t set this property: '.$method);
				}
			$method = 'set_'.$method;
			$output = call_user_func_array(array($this,$method),$args);
			return ($output === null) ? $output : $this;
			}
		else
			{
			if (isset($this->$method) && $this->$method !== null) {return $this->$method;}
			$method = 'get_'.$method;
			return $this->$method();
			}
		}
	public function __isset($property)
		{
		return isset($this->$property);
		}
	
	public function get_location()
		{
		return $this->_location;
		}
	public function set_location($path)
		{
		$this->folder = null;
		$this->name = null;
		$this->extension = null;
		$this->mimetype = null;
		rename($this->_location,$path);
		$this->_location = $path;
		}
		
	public function get_folder()
		{
		return $this->folder = substr($this->location,0,strrpos($this->location,'/')+1);
		}
	public function set_folder($folder)
		{
		$this->folder = $folder;
		$this->location = $this->folder.$this->name;
		}
		
	public function get_name()
		{
		return $this->name = substr($this->location,strrpos($this->location,'/')+1);
		}
	public function set_name($name)
		{
		$this->name = $name;
		$this->extension = null;
		$this->mimetype = null;
		$this->location = $this->folder.$this->name;
		}
		
	public function get_extension()
		{
		return $this->extension = substr($this->name(),strrpos($this->name(),'.')+1);
		}
	public function set_extension($ext)
		{
		$this->name = substr($this->name(),0,strrpos($this->name(),'.')+1).$ext;
		$this->mimetype = null;
		$this->location = $this->folder().$this->name();
		}
		
	public function get_mimetype()
		{
		return $this->mimetype = isset(s('config')->mimes[$this->extension()]) ? s('config')->mimes[$this->extension()] : s('config')->mimes['_default_file'];
		}
		
	public function get_group()
		{
		return filegroup($this->location);
		}
	public function set_group($group)
		{
		return chgrp($this->location,$group);
		}
		
	public function get_mode()
		{
		return fileperms($this->location);
		}
	public function set_mode($mode)
		{
		return chmod($this->location,$mode);
		}
		
	public function get_owner()
		{
		return fileowner($this->location);
		}
	public function set_owner($owner)
		{
		return chown($this->location,$owner);
		}
		
	public function exists()
		{
		return file_exists($this->location);
		}
	public function copy($location)
		{
		copy($this->location,$location);
		return new file($location);
		}
	public function symlink($location)
		{
		return symlink($location,$this->location);
		}
	public function hardlink($location)
		{
		return link($this->location,$location);
		}
	public function touch($time = null,$atime = null)
		{
		if ($time === null) {$time = time();}
		if ($atime === null) {$atime = $time;}
		return touch($this->location,$time,$atime);
		}
	public function delete()
		{
		return unlink($this->location);
		}
	public function read()
		{
		return file_get_contents($this->location);
		}
	public function write($string,$flags = 0)
		{
		file_put_contents($this->location,$string,$flags);
		return $this;
		}
	public function append($string)
		{
		return $this->write($string,FILE_APPEND);
		}
	public function prepend($string)
		{
		return $this->write($string.$this->read());
		}
	public function passthru()
		{
		echo $this->read();
		}
	public function run()
		{
		include($this->location);
		}
	public function size()
		{
		return filesize($this->location);
		}
	public function modtime()
		{
		return filemtime($this->location);
		}
	public function changetime()
		{
		return filectime($this->location);
		}
	public function accesstime()
		{
		return fileatime($this->location);
		}
	public function inode()
		{
		return fileinode($this->location);
		}
	public function realpath()
		{
		return realpath($this->location);
		}
	
	public function __toString()
		{
		return file_get_contents($this->location);
		}
	public function move($path){$this->location($path);}
	public function chmod($mode){$this->mode($mode);}
}
