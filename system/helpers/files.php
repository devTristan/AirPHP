<?php
class files extends helper {
	static public function ls($dir)
		{
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
}
