<?php
abstract class controller extends base {
	public function __get($classname)
		{
		return s($classname);
		}
}
