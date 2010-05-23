<?php
class codeigniter extends helper {
	static public function add_helper($helper)
		{
		if (is_array($helper))
			{
			foreach ($helper as $one)
				{
				self::add_helper($one);
				}
			}
		else
			{
			require_once(DIR_COMPATIBILITY.'codeigniter/helpers/'.$helper.'_helper.php');
			}
		return self;
		}
	static public function add_plugin($plugin)
		{
		if (is_array($plugin))
			{
			foreach ($plugin as $one)
				{
				self::add_plugin($one);
				}
			}
		else
			{
			require_once(DIR_COMPATIBILITY.'codeigniter/plugins/'.$plugin.'_pi.php');
			}
		return self;
		}
	static public function library($name)
		{
		return s('CI_'.ucfirst($name));
		}
}
