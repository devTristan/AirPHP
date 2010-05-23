<?php
class html extends helper {
	public function __call($name,$args = array())
		{
		if (count($args) == 1 && is_array($args[0]))
			{
			$args = $args[0];
			}
		if ($count($args) == 0)
			{
			return '<'.$name.'></'.$name.'>';
			}
		if (isset($args[0]))
			{
			//list
			}
		else
			{
			//dict
			}
		}
}
