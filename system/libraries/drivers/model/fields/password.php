<?php
class field_password extends basefield {
	public function set($value)
		{
		return sha1($value);
		}
	public function length($args)
		{
		return 40;
		}
	public function type($type,$args)
		{
		return 'char';
		}
}
