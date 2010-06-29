<?php
function stripslashes_recursive(&$value)
	{
	return $value = (is_array($value)) ? array_map('stripslashes_recursive', $value) : stripslashes($value);
	}
stripslashes_recursive($_GET);
stripslashes_recursive($_POST);
stripslashes_recursive($_COOKIE);
