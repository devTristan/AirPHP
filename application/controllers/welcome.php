<?php
class controller_welcome extends controller {
	public function index()
		{
		s('output')->cache(1);
		s('views')->show_view('welcome_message');
		}
	public function css()
		{
		s('output')->cache(date::years(10));
		s('views')->show_view('airphp_style');
		}
}
