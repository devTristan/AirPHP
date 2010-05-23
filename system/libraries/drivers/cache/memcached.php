<?php
class cache_memcached extends driver {
private $link;
	public function __construct()
		{
		$this->link = memcache_connect('localhost', 11211);
		}
	public function get($item)
		{
		return memcache_get($this->link, $item);
		}
	public function set($item,$value,$time = -1)
		{
		if ($time == -1) {$time = 2592000;}
		memcache_set($this->link, $item, $value, 0, $time);
		return true;
		}
	public function exists($item)
		{
		return ($this->get($item)) ? true : false;
		}
	public function remove($item)
		{
		memcache_delete($this->link, $item);
		}
	public function clear()
		{
		memcache_flush($this->link);
		}
}
