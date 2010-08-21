<?php
class field_text extends basefield {
	public function args($args)
		{
		return isset($args[0]) ? $args[0] : false;
		}
}
