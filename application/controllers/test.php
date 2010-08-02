<?php
class controller_test extends controller {
	public function index()
		{
		s('output')->cache(1);
		s('views')->show_view('test/index',array('pages' => array(
			array('name' => 'DB Test', 'url' => URL_BASE.'test/db'),
			array('name' => 'Cache Test', 'url' => URL_BASE.'test/cache'),
			array('name' => 'Config Test', 'url' => URL_BASE.'test/config'),
			array('name' => 'Email Test', 'url' => URL_BASE.'test/email'),
			array('name' => 'Lorem Ipsum Test', 'url' => URL_BASE.'test/loremipsum'),
			array('name' => 'Model Test', 'url' => URL_BASE.'test/model'),
			array('name' => 'Globals Test', 'url' => URL_BASE.'test/globals'),
			array('name' => 'String Helper Test', 'url' => URL_BASE.'test/str'),
			array('name' => 'Alternator Test', 'url' => URL_BASE.'test/alternator'),
			array('name' => 'Download Helper Test', 'url' => URL_BASE.'test/download'),
			array('name' => 'Session Test', 'url' => URL_BASE.'test/session')
			)));
		}
	public function db()
		{
		$this->msg('DB Test',nl2br(s('db')->status()));
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
		$out = '<pre>'.$this->models->users->create().'</pre>';
		$user = $this->models->users->get_one_by_id(1);
		$out .= '<pre>'.$user->print_r(true).'</pre>';
		$user['join_date'] += 1;
		$user['last_visit'] += 1;
		$user['join_ip'] = '127.0.0.1';
		$user->commit();
		$out .= '<table><thead><th>Time</th><th>SQL</th></thead><tbody>';
		foreach (s('db')->querylist as $query)
			{
			$out .= '<tr><td>'.number_format($query[0],5).'</td><td><pre>'.$query[1].'</pre></td></tr>';
			}
		$out .= '</tbody></table>';
		$this->msg('Model Test',$out);
		}
	public function globals()
		{
		$msg = '<h3>$_GET</h3><pre>'.print_r($_GET,true).'</pre>';
		$msg .= '<h3>$_POST</h3><pre>'.print_r($_POST,true).'</pre>';
		$msg .= '<h3>$_SERVER</h3><pre>'.print_r($_SERVER,true).'</pre>';
		$this->msg('Globals Test',$msg);
		}
	public function str()
		{
		$msg = array();
		
		$msg['100 Random Alphanumeric Chars'] = str::random(str::alphanumeric,100);
		$msg['"I love 4 cakes" removed all but non-lowercase-letters'] = str::allow('I love 4 cakes',str::lower);
		$msg['Does "muffin" begin with "muf"?'] = (str::beginswith('muffin','muf')) ? 'yes' : 'no';
		$msg['Does "Pie" begin with "p"?'] = (str::beginswith('Pie','p')) ? 'yes' : 'no';
		
		$hash = str::hash('secret password',$salt);
		$msg['Hash of "secret password"'] = $hash;
		$msg['Salt of "secret password" hash'] = $salt;
		
		$encrypted = str::encrypt('muffincake','secret');
		$msg['"muffincake" encrypted with "secret"'] = $encrypted;
		$msg['"'.$encrypted.'" decrypted with "secret"'] = str::decrypt($encrypted,'secret');
		
		$msgstr = '';
		foreach ($msg as $title => $out)
			{
			$msgstr .= '<h3>'.str::htmlescape($title).'</h3><pre>'.str::htmlescape($out).'</pre>';
			}
		$this->msg('String Helper Test',$msgstr);
		}
	public function alternator()
		{
		$alt = new alternator('#500','#800','#A00');
		$msg = '';
		for ($i = 0; $i < 20; $i++)
			{
			$msg .= '<div style="background:'.$alt.'">&nbsp;</div>';
			}
		$this->msg('Alternator Test',$msg);
		}
	public function loremipsum()
		{
		$this->msg('Typography Test',$this->loremipsum->paragraphs(4,'html'));
		}
	public function download()
		{
		if (isset($_GET['go']))
			{
			download::file(__FILE__);
			}
		else
			{
			$this->msg('Download Helper Test', '<a href="download?go">Download test.php</a>');
			}
		}
	public function session()
		{
		if (!isset($this->session['views']))
			{
			$this->session['views'] = 0;
			}
		$this->session['views'] += 1;
		$this->msg('Session Test', '<pre>'.$this->session->print_r(true).'</pre>');
		}
	private function msg($title,$message)
		{
		s('views')->show_view('errors/general',array(
			'heading' => $title,
			'message' => '<a href="./">&laquo; Back to tests</a> | <a href="'.URL_BASE.URL.'">Re-run</a><br/>'.$message
			));
		}
}
