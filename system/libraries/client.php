<?php
class client extends library {
	public function __get($field)
		{
		$this->$field = $this->$field();
		}
	private function ip()
		{
		return $_SERVER['REMOTE_ADDR'];
		}
}
