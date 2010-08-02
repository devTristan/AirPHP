<?php
class cache_flatfile extends driver {
protected $prefix = 'flatcache_';
	public function get($item)
		{
		return ($this->exists($item)) ? file_get_contents(DIR_CACHE.$this->prefix.$item) : null;
		}
	public function set($item,$value)
		{
		file_put_contents(DIR_CACHE.$this->prefix.$item,$value);
		return true;
		}
	public function exists($item)
		{
		return file_exists(DIR_CACHE.$this->prefix.$item);
		}
	public function remove($item)
		{
		if (!$this->exists($item)) {return;}
		unlink(DIR_CACHE.$this->prefix.$item);
		}
	public function clear()
		{
		foreach (glob(DIR_CACHE.$this->prefix.'*') as $file)
			{
			unlink($file);
			}
		}
}
