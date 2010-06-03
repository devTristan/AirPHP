<?php
class field_varchar extends basefield {
	public function length($args)
		{
		return $args[0];
		}
}
