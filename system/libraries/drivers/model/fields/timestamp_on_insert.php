<?php
class field_timestamp_on_insert extends basefield {
	public function type($type,$args)
		{
		return 'int';
		}
	public function length($args)
		{
		return 10;
		}
	public function unsigned($args)
		{
		return true;
		}
	public function insert($vars)
		{
		return time();
		}
}
