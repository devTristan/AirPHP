<?php
class CI_Lang {
private $lang;
private $loaded;
	public function load($file)
		{
		if (!isset($this->loaded[$file]))
			{
			$this->loaded[$file] = false;
			$lang = $this->lang;
			include(DIR_COMPATIBILITY.'codeigniter/language/'.s('config')->language.'/'.$file.'_lang.php');
			$this->lang = $lang;
			}
		}
	public function line($field)
		{
		return (isset($this->lang[$field])) ? $this->lang[$field] : $field;
		}
}
