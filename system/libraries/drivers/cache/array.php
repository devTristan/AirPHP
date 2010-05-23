<?php
class cache_array extends driver {
private $cache = array();
	public function get($item)
		{
		if (!isset($this->cache[$item]))
			{
			return false;
			}
		return $this->cache[$item];
		}
	public function set($item,$value)
		{
		if ($this->exists($item) && $this->cache[$item] == $value)
			{
			return false;
			}
		$this->cache[$item] = $value;
		return true;
		}
	public function exists($item)
		{
		return isset($this->cache[$item]);
		}
	public function remove($item)
		{
		unset($this->cache[$item]);
		}
	public function clear()
		{
		$this->cache = array();
		}
}
