<?php
class field_timestamp_on_insert extends basefield {
	public function type($args)
		{
		return 'int';
		}
	public function args($args)
		{
		return 10;
		}
	public function unsigned($args)
		{
		return true;
		}
	public function insert($args, $vars)
		{
		return time();
		}
	public function format($value, $field, $args)
		{
		return date($args[0], $value);
		}
}
