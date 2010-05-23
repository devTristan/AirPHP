<?php
class controller_test extends controller {
	public function index()
		{
		s('views')->show_view('test/index',array('pages' => array(
			array('name' => 'DB Test', 'url' => URL_BASE.'test/db'),
			array('name' => 'Cache Test', 'url' => URL_BASE.'test/cache'),
			array('name' => 'Config Test', 'url' => URL_BASE.'test/config')
			)));
		}
	public function db()
		{
		$version = s('db')->fetch_enum(s('db')->query('SELECT VERSION()'));
		$version = $version[0];
		$this->msg('DB Test','Version: '.$version.'<br/>'.str_replace('  ','<br/>',s('db')->status()));
		}
	public function config()
		{
		$config = s('config');
		$this->msg('Config Test','<pre>$config = '.var_export($config->conf,true).';</pre>');
		}
	public function cache()
		{
		$msg = array();
		$cache = new cache('testcache', 'array', 'file');
		$msg[] = 'Created cache object using array and file drivers';
		$cache->cake = 5;
		$msg[] = 'Set cache->cake to 5';
		$msg[] = 'Got cache->cake as '.$cache->cake;
		$cache->clear('array');
		$msg[] = 'Cleared array cache driver';
		$msg[] = 'Got cache->cake as '.$cache->cake;
		$msg[] = 'Checked to see if cache->cake is set: '.((isset($cache->cake)) ? 'true' : 'false');
		unset($cache->cake);
		$msg[] = 'Unset cache->cake';
		$msg[] = 'Checked to see if cache->cake is set: '.((isset($cache->cake)) ? 'true' : 'false');
		$msg[] = 'Got cache->cake as '.((($cache->cake) === null) ? 'null' : $cache->cake);
		$cache->cake = 5;
		$msg[] = 'Set cache->cake to 5';
		$msg[] = 'Got cache->cake as '.$cache->cake;
		$cache->clear('file');
		$msg[] = 'Cleared file cache driver';
		$msg[] = 'Got cache->cake as '.$cache->cake;
		$cache->cake = 6;
		$msg[] = 'Set cache->cake to 6';
		$msg[] = 'Got cache->cake as '.$cache->cake;
		$cache->clear();
		$msg[] = 'Cleared cache';
		$msg[] = 'Checked to see if cache->cake is set: '.((isset($cache->cake)) ? 'true' : 'false');
		$msg = implode('<br/>',$msg);
		$this->msg('Cache Test',$msg);
		}
	private function msg($title,$message)
		{
		s('views')->show_view('errors/general',array(
			'heading' => $title,
			'message' => '<a href="./">&laquo; Back to tests</a> | <a href="'.URL_BASE.URL.'">Re-run</a><br/><br/>'.$message
			));
		}
}