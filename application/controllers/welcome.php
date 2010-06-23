<?php
class controller_welcome extends controller {
	public function index()
		{
		$this->views->show_view('welcome_message');
		}
	public function css()
		{
		$this->output->cache(date::years(10));
		$this->output->client_cache(date::years(10));
		$this->views->show_view('airphp_style');
		}
}
