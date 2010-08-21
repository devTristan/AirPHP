<?php
class obj_client extends obj {
	public function __construct($user_agent, $ip = null)
		{
		$this->user_agent = $user_agent;
		$this->ip = $ip;
		}
	public function __get($field)
		{
		$this->$field = $this->$field();
		}
	private function browser() {}
	private function os() {}
	private function supported_fonts() {}
}
