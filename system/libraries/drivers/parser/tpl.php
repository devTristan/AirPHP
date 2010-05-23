<?php
class parser_tpl extends driver {
	public function __call($method,$data)
		{
		return str_replace('CodeIgniter','Cancer',$data);
		}
}
