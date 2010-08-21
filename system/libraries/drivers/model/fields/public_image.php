<?php
class field_public_image extends basefield {
	public function get($value, $args)
		{
		return URL_PUBLIC_STORAGE.$value;
		}
	public function set($value)
		{
		$file = new file($value);
		$file->copy(DIR_PUBLIC_STORAGE.$file->name);
		return $file->name;
		}
	public function type($args)
		{
		return 'text';
		}
}
