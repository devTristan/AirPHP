<?php
class str {
const alphanumeric = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
const numeric = '0123456789';
const nonzero = '123456789';
const binary = '01';
const hex = '0123456789ABCDEF';
const upper = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
const lower = 'abcdefghijklmnopqrstuvwxyz';
const letters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
const symbols = '~`!@#$%^&*()-_=+,.<>/?;:[]{}\|\'"';
const spacing = " \t\n";
private $alternatepos = array();
	static public function allow($str,$dummy)
		{
		$str = str_split($str);
		$allowed = array();
		$args = func_get_args();
		unset($args[0]);
		foreach ($args as $arg)
			{
			if (is_string($arg)) {$arg = str_split($arg);}
			foreach ($arg as $char_id => $char)
				{
				if (strlen($char) > 1)
					{
					foreach (str_split($char) as $char)
						{
						$allowed[$char] = false;
						}
					}
				else
					{
					$allowed[$char] = false;
					}
				}
			}
		$newstr = '';
		foreach ($str as $char_id => $char)
			{
			if (isset($allowed[$char]))
				{
				$newstr .= $char;
				}
			}
		return $newstr;
		}
	public static function beginswith($str,$begins)
		{
		return (substr($str,0,strlen($begins)) == $begins);
		}
	public static function endswith($str,$ends)
		{
		return (substr($str,-strlen($ends)) == $ends);
		}
	public static function random($chars, $length = 1)
		{
		$out = '';
		$i = 0;
		$charlen = strlen($chars)-1;
		while ($i < $length)
			{
			$out .= substr($chars,rand(0,$charlen),1);
			$i++;
			}
		return $out;
		}
	public static function hash($str, &$salt = null, $saltlength = 10, $chars = null)
		{
		if ($chars === null) {$chars = self::alphanumeric.self::symbols;}
		if ($salt === null)
			{
			$salt = self::random($chars, $saltlength);
			}
		return sha1(s('config')->hash_salt.$str.$salt);
		}
	public static function htmlescape($str)
		{
		return htmlspecialchars($str);
		}
	public static function encrypt($str, $key, $algorithm = MCRYPT_RIJNDAEL_256)
		{
		$iv = mcrypt_create_iv(mcrypt_get_iv_size($algorithm, MCRYPT_MODE_ECB), MCRYPT_RAND);
		return base64_encode(mcrypt_encrypt($algorithm, $key, $str, MCRYPT_MODE_ECB, $iv));
		}
	public static function decrypt($str, $key, $algorithm = MCRYPT_RIJNDAEL_256)
		{
		$iv = mcrypt_create_iv(mcrypt_get_iv_size($algorithm, MCRYPT_MODE_ECB), MCRYPT_RAND);
		return mcrypt_decrypt($algorithm, $key, base64_decode($str), MCRYPT_MODE_ECB, $iv);
		}
	public static function contains($haystack, $needle)
		{
		return (strpos($haystack, $needle) !== false);
		}
	public static function readable($str)
		{
		if ($str === null) {return 'NULL';}
		if ($str === false) {return 'FALSE';}
		if ($str === true) {return 'TRUE';}
		return $str;
		}
}
