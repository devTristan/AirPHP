<?php
class field_timestamp extends basefield {
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
	public function format($value, $field, $args)
		{
		return date($args[0], $value);
		}
}
