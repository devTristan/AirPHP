<?php
require_once(DIR_DRIVERS.'cache/flatfile.php');
class cache_file extends cache_flatfile {
	public function __construct()
		{
		$this->prefix = 'cache_';
		parent::__construct();
		}
	public function get($item)
		{
		$data = parent::get($item);
		$nlpos = strpos($data,"\n");
		$expires = (int) substr($data,0,$nlpos);
		if ($expires != -1 && $expires < time())
			{
			$this->remove($item);
			return null;
			}
		return unserialize(substr($data,$nlpos+1));
		}
	public function set($item,$value,$time = -1)
		{
		if ($time != -1)
			{
			$time = time()+$time;
			}
		return parent::set($item,$time."\n".serialize($value));
		}
}
