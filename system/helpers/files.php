<?php
class files extends helper {
private static $sizes = array(
	'kb' => 1,
	'mb' => 2,
	'gb' => 3,
	'tb' => 4,
	'pb' => 5,
	'eb' => 6,
	'zb' => 7,
	'yb' => 8
	);
private static $tmpfile_length = 5;
	static public function ls($dir = null)
		{
		if ($dir === null) {$dir = getcwd();}
		$results = array();
		$handler = opendir($dir);
		while ($file = readdir($handler))
			{
			if ($file != '.' && $file != '..')
				{
				$results[] = $file;
				}
			}
		closedir($handler);
		return $results;
		}
	static public function readable_bytes($bytes, $precision = 2)
		{
		$units = array('','K','M','G','T','P','E','Z','Y');
		$bytes = max($bytes, 0);
		$pow = floor(($bytes ? log($bytes) : 0) / log(1024));
		$pow = min($pow, count($units) - 1);
		$bytes /= pow(1024, $pow);
		return round($bytes, $precision) . ' ' . $units[$pow].'B';
		}
	static public function tmp($prefix = DIR_STORAGE, $suffix = '', &$token)
		{
		do $token = str::random( str::alphanumeric, self::$tmpfile_length );
		while ( file_exists($prefix.$token.$suffix) );
		
		return $prefix.$token.$suffix;
		}
	static public function __callStatic($method, $args)
		{
		$count = (isset($args[0])) ? $args[0] : 1;
		return $count * pow(1024, self::$sizes[$method]);
		}
}
