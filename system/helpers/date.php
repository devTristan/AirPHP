<?php
class date extends helper {
private static $times = array(
	'second' => 1,
	'minute' => 60,
	'hour' => 3600,
	'day' => 86400,
	'week' => 604800,
	'year' => 31536000
	);
	public static function span($seconds, $max = 3, $delim = ', ', $sep = ' ', $names = null, $plural = 's')
		{
		if ($names === null) {$names = self::$times;}
		$out = array();
		foreach (array_reverse($names) as $name => $sec)
			{
			if ($seconds >= $sec)
				{
				$found = floor($seconds/$sec);
				$seconds -= $found*$sec;
				$out[] = $found.$sep.$name.(($found == 1) ? '' : $plural);
				if (count($out) == $max) {break;}
				}
			}
		$out = implode($delim, $out);
		return $out;
		}
	public static function __callStatic($method,$args)
		{
		$args[0] = (isset($args[0])) ? $args[0] : 1;
		if (substr($method,-1) == 's' && in_array(substr($method,0,-1),array_keys(self::$times)))
			{
			return self::$times[substr($method,0,-1)]*$args[0];
			}
		}
}
