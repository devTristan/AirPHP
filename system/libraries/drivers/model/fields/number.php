<?php
class field_number extends basefield {
	public function type($args)
		{
		if (!$args)
			{
			$args = array(4294967295);
			}
		$min = (count($args) == 1) ? 0 : $args[0];
		$max = (count($args) == 1) ? $args[0] : $args[1];
		$brackets = array(
			'tinyint' => 255,
			'smallint' => 65535,
			'mediumint' => 16777215,
			'int' => 4294967295,
			'bigint' => 18446744073709551615
			);
		$unsigned = $this->unsigned($args);
		if ($unsigned)
			{
			foreach ($brackets as $field => $bracket)
				{
				if ($max <= $bracket)
					{
					return $field;
					}
				}
			}
		else
			{
			foreach ($brackets as $field => $bracket)
				{
				$bracketmax = floor($bracket/2);
				$bracketmin = -ceil($bracket/2);
				if ($max <= $bracketmax && $min >= $bracketmin)
					{
					return $field;
					}
				}
			}
		throw new NumberTooBigException('Can\'t do numbers that big. Min: '.$min.', Max: '.$max);
		return false;
		}
	public function args($args)
		{
		if (!$args)
			{
			$args = array(4294967295);
			}
		$min = (count($args) == 1) ? 0 : $args[0];
		$max = (count($args) == 1) ? $args[0] : $args[1];
		return max(strlen(abs($min)),strlen($max));
		}
	public function unsigned($args)
		{
		if (!$args)
			{
			$args = array(4294967295);
			}
		$min = (count($args) == 1) ? 0 : $args[0];
		return ($min >= 0);
		}
}
class NumberTooBigException extends Exception {}
