<?php
class ip extends helper {
	public static function from_int($ip)
		{
		$ip = decbin($ip);
		$ip = str::pad_left($ip, 32, '0');
		$ip = str_split($ip, 8);
		$ip = array_map('bindec', $ip);
		$ip = implode('.', $ip);
		return $ip;
		}
	public static function to_int($ip)
		{
		$ip = explode('.', $ip);
		$newip = array();
		foreach ($ip as $segment)
			{
			$newip[] = str::pad_left(decbin($segment),8,'0');
			}
		$newip = implode('', $newip);
		$newip = bindec($newip);
		return $newip;
		}
}
