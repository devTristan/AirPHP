<?php
class http extends helper {
	public static function build_query($params)
		{
		if (!$params) {return '';}
		$query = array();
		foreach ($params as $field => $value)
			{
			$query[] = urlencode($field).'='.urlencode($value);
			}
		return '?'.implode('&',$query);
		}
}
