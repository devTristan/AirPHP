<?php
class controller_test extends controller {
	public function index()
		{
		s('views')->show_view('test/index',array('pages' => array(
			array('name' => 'DB Test', 'url' => URL_BASE.'test/db'),
			array('name' => 'Cache Test', 'url' => URL_BASE.'test/cache'),
			array('name' => 'Config Test', 'url' => URL_BASE.'test/config'),
			array('name' => 'Email Test', 'url' => URL_BASE.'test/email'),
			array('name' => 'Lorem Ipsum Test', 'url' => URL_BASE.'test/loremipsum')
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
	public function email()
		{
		$this->CI_Email->from('cake@magistream.com', 'George');
		$this->CI_Email->to('luck.lil.leprechaun@gmail.com');
		$this->CI_Email->subject('Email Test');
		$this->CI_Email->message('Testing the email class.');
		$this->CI_Email->send();
		$this->msg('Email Test', $this->CI_Email->print_debugger());
		}
	public function model()
		{
		$out = '<pre>'.s('models')->users->create().'</pre>';
		$out .= '<pre>'.s('db')->build_select(array(
			'fields' => array('t1.cake','pie','`t2`.`chicken`'),
			'db' => 'mydatabase',
			'table' => array('t1','t2'),
			'where' => array(
				'cake' => 'pie',
				'OR',
				'id' => 4,
				'OR',
				array(
					'cake' => 'chicken',
					'id' => 37.5,
					'nuggets' => false,
					array(
						'pie' => 7,
						'or',
						'snuggle' => 'puff'
						)
					)
				),
			'group' => 'groupbyfield',
			'order' => array('id','cake'),
			'limit' => array(10,50)
			)).'</pre>';
		$out .= '<pre>'.s('db')->build_update(array(
			'db' => 'airphp',
			'table' => 'table1',
			'set' => array('cake' => null,'pie' => 4),
			'where' => array('cake' => 'happy muffins'),
			'limit' => 1
			)).'</pre>';
		$this->msg('Model Test',$out.number_format(s('timing')->elapsed('total'),4));
		}
	public function loremipsum()
		{
		$this->msg('Typography Test',$this->loremipsum->paragraphs(4,'html'));
		}
	private function msg($title,$message)
		{
		s('views')->show_view('errors/general',array(
			'heading' => $title,
			'message' => '<a href="./">&laquo; Back to tests</a> | <a href="'.URL_BASE.URL.'">Re-run</a><br/>'.$message
			));
		}
}
