<?php
class client extends obj_client {
	public function __construct()
		{
		parent::__construct($_SERVER['HTTP_USER_AGENT'], $_SERVER['REMOTE_ADDR']);
		}
}
