<?php
class field_json extends basefield {
	public function set($value)
		{
		return json_encode($value);
		}
	public function get($value)
		{
		return json_decode($value, true);
		}
	public function type($args)
		{
		return 'text';
		}
}
