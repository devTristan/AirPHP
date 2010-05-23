<?php
class controller_welcome extends controller {
	public function index()
		{
		s('views')->show_view('welcome_message');
		}
	public function css()
		{
		s('views')->show_view('airphp_style');
		}
}
