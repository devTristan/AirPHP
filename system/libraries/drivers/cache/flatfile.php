<?php
class cache_flatfile extends driver {
private $prefix = 'cache_flat_';
	public function __construct()
		{
		$this->prefix = DIR_CACHE.$this->prefix;
		}
	public function get($item)
		{
		return ($this->exists($item)) ? file_get_contents($this->prefix.$item) : null;
		}
	public function set($item,$value)
		{
		file_put_contents($this->prefix.$item,$value);
		return true;
		}
	public function exists($item)
		{
		return file_exists($this->prefix.$item);
		}
	public function remove($item)
		{
		if (!$this->exists($item)) {return;}
		unlink($this->prefix.$item);
		}
	public function clear()
		{
		foreach (glob($this->prefix.'*') as $file)
			{
			unlink($file);
			}
		}
}
