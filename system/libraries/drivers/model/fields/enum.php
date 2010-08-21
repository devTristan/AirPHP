<?php
class field_enum extends basefield {
	public function type($args)
		{
		return 'enum';
		}
	public function args($args)
		{
		return $args;
		}
}
