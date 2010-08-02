<?php
class lang extends library {
	public function __get($file)
		{
		return s('langfile', $file);
		}
}
class langfile extends library {
private $data = array();
private $file;
	public function __construct($file)
		{
		$this->file = $file;
		}
	public function __call($item, $args)
		{
		$language = s('config')->language;
		if (!isset($this->data[$language]))
			{
			$this->data[$language] = @include(DIR_LANGUAGE.$language.'/'.$this->file.'.php');
			if ($this->data[$language] === false)
				{
				show_error(s('lang')->errors->invalid_langfile($language.'/'.$this->file));
				}
			}
		if (isset($this->data[$language][$item]))
			{
			array_unshift($args, $this->data[$language][$item]);
			return call_user_func_array('sprintf', $args);
			}
		else
			{
			return $item;
			}
		}
	public function __get($item)
		{
		return $this->$item();
		}
}
