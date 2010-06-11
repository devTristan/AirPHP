<?php
class number extends helper {
static private $names = array(
	'second' => 1,
	'minute' => 60,
	'hour' => 3600,
	'day' => 86400,
	'week' => 604800,
	'year' => 31536000
	);
	static public function timespan($seconds, $max = 3, $delim = ', ', $sep = ' ', $names = null, $plural = 's')
		{
		if ($names === null) {$names = self::$names;}
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
	static public function format($number, $decimal = 3, $leading_zeroes = 0, $decimal_point = '.', $thousands_sep = ',')
		{
		
		}
}
