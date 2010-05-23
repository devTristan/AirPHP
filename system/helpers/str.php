<?php
class str {
const alphanumeric = ' abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
const numeric = '1234567890';
const upper = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
const lower = 'abcdefghijklmnopqrstuvwxyz';
const symbols = '~`!@#$%^&*()-_=+,.<>/?;:[]{}\|\'"';
const spacing = " \t\n";
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
}
