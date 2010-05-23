<?php
class input extends library {
	public function __get($field)
		{
		switch ($field)
			{
			case 'get': return s('requestarray',$_GET);
			case 'post': return s('requestarray',$_POST);
			case 'request': return s('requestarray',$_REQUEST);
			case 'files': return s('requestfiles');
			default: return null;
			}
		}
	public function __set($field,$value)
		{
		switch ($field)
			{
			case 'get': $_GET = $value; break;
			case 'post': $_POST = $value; break;
			case 'request': $_REQUEST = $value; break;
			case 'files': $_FILES = $value; break;
			default: return null;
			}
		}
	public function __isset($field)
		{
		return (in_array($field,array('get','post','request','files')));
		}
}
