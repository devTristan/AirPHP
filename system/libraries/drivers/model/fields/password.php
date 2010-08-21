<?php
class field_password extends basefield {
	public function set($value)
		{
		return sha1($value);
		}
	public function args($args)
		{
		return 40;
		}
	public function type($args)
		{
		return 'char';
		}
	public function format($value, $field, $args)
		{
		return ($value == sha1($args[0]));
		}
}
