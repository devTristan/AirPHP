<?php
class field_ip extends basefield {
	public function set($value)
		{
		return ip::to_int($value);
		}
	public function get($value)
		{
		return ip::from_int($value);
		}
	public function length($args)
		{
		return 10;
		}
	public function type($type,$args)
		{
		return 'int';
		}
	public function unsigned($args)
		{
		return true;
		}
}
