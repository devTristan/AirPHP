<?php

	/*
		dAmn version 4 by photofroggy.
		
		Released under a Creative Commons Attribution-Noncommercial-Share Alike 3.0 License, which allows you to copy, distribute, transmit, alter, transform,
		and build upon this work but not use it for commercial purposes, providing that you attribute me, photofroggy (froggywillneverdie@msn.com) for its creation.
		
		This class handles dAmn sockets and reads data from a dAmn connection. Can be
		used with any PHP client. I guess this is almost an unintended implementation of the
		dAmnSock specification proposed by Zeros-Elipticus, or as close as I will willingly
		come to it.
		
		To create a new instance of the class simply use $variable = new dAmn;
		
		To get a cookie you need to use $dAmn->getCookie($username, $password);
		and store the cookie in $dAmn->cookie;. If you don't do this then you won't
		be able to get connected to dAmn or any chat network.
		
		To be able to actually get logged into deviantART and connected to dAmn you
		need to set some variables from outside the class.
		
		EXAMPLE:
			$dAmn->Client = 'dAmn/public/3';
			$dAmn->owner = 'photofroggy';
			$dAmn->trigger = '!';
		
		Now when you use $dAmn->connect();, that info will be sent in the handshake!
		
		Use $dAmn->read(); to read data from the socket. If packets are received
		then the packets are returned in an array. If nothing is really happening
		on the socket then false is returned.
	*/
	
class dAmn {
	const LBR = "\n";
	public $Ver = 4;
	public $server = array(
		'chat' => array(
			'host' => 'chat.deviantart.com',
			'version' => '0.3',
			'port' => 3900,
			),
		'login' => array(
			'transport' => 'ssl://',
			'host' => 'www.deviantart.com',
			'file' => '/users/login',
			'port' => 443,
			)
		);
	public $Client = 'dAmn';
	public $Agent = 'dAmn/4';
	public $owner = 'photofroggy';
	public $trigger = '!';
	public $socket = null;
	public $cookie = null;
	public $connecting = null;
	public $login = null;
	public $connected = null;
	public $close = null;
	public $buffer = null;
	public $chat = array();
	public $disconnects = 0;
	private static $tablumps = array(
		// Regex stuff for removing tablumps.
		'a1' => array(
			"&b\t",  "&/b\t",    "&i\t",    "&/i\t", "&u\t",   "&/u\t", "&s\t",   "&/s\t",    "&sup\t",    "&/sup\t", "&sub\t", "&/sub\t", "&code\t", "&/code\t",
			"&br\t", "&ul\t",    "&/ul\t",  "&ol\t", "&/ol\t", "&li\t", "&/li\t", "&bcode\t", "&/bcode\t",
			"&/a\t", "&/acro\t", "&/abbr\t", "&p\t", "&/p\t"
			),
		'a2' => array(
			"<b>",  "</b>",       "<i>",     "</i>", "<u>",   "</u>", "<s>",   "</s>",    "<sup>",    "</sup>", "<sub>", "</sub>", "<code>", "</code>",
			"\n",   "<ul>",       "</ul>",   "<ol>", "</ol>", "<li>", "</li>", "<bcode>", "</bcode>",
			"</a>", "</acronym>", "</abbr>", "<p>",  "</p>\n"
			),
		'b1' => array(
			"/&emote\t([^\t]+)\t([^\t]+)\t([^\t]+)\t([^\t]+)\t([^\t]+)\t/",
			"/&a\t([^\t]+)\t([^\t]*)\t/",
			"/&link\t([^\t]+)\t&\t/",
			"/&link\t([^\t]+)\t([^\t]+)\t&\t/",
			"/&dev\t[^\t]\t([^\t]+)\t/",
			"/&avatar\t([^\t]+)\t[0-9]\t/",
			"/&thumb\t([0-9]+)\t([^\t]+)\t([^\t]+)\t([^\t]+)\t([^\t]+)\t([^\t]+)\t([^\t]+)\t/",
			"/&img\t([^\t]+)\t([^\t]*)\t([^\t]*)\t/",
			"/&iframe\t([^\t]+)\t([0-9%]*)\t([0-9%]*)\t&\/iframe\t/",
			"/&acro\t([^\t]+)\t/",
			"/&abbr\t([^\t]+)\t/"
			),
		'b2' => array(
			"\\1",
			"<a href=\"\\1\" title=\"\\2\">",
			"\\1",
			"\\1 (\\2)",
			":dev\\1:",
			":icon\\1:",
			":thumb\\1:",
			"<img src=\"\\1\" alt=\"\\2\" title=\"\\3\" />",
			"<iframe src=\"\\1\" width=\"\\2\" height=\"\\3\" />",
			"<acronym title=\"\\1\">",
			"<abbr title=\"\\1\">"
			)
		);

	public function __construct()
		{
		// Before anything happens, we need to make sure OpenSSL is loaded. If not, kill the program!
		if (!extension_loaded('OpenSSL'))
			{
			$this->Warning('WARNING: You don\'t have OpenSSL loaded!');
			$this->Error('Enable OpenSSL before running this application!');
			}
		}
	
	public function Time($ts=false) {return date('H:i:s', ($ts===false?time():$ts));}
	public function Clock($ts=false) {return '['.$this->Time($ts).']';}
	public function Message($str = '', $ts = false) {s('console')->log($str);}
	public function Notice($str = '', $ts = false)  {$this->Message('** '.$str,$ts);}
	public function Warning($str = '', $ts = false) {$this->Message('>> '.$str,$ts);}
	public function Error($str = '', $ts = false) {$this->Message('>> '.$str,$ts);die();}
	public function getCookie($username, $pass)
		{
		// Method to get the cookie! Yeah! :D
		// Our first job is to open an SSL connection with our host.
		$socket = fsockopen(
			$this->server['login']['transport'].$this->server['login']['host'],
			$this->server['login']['port']
			);
		// If we didn't manage that, we need to exit!
		if ($socket === false)
			{
			$this->Warning('Could not open a connection!');
			$this->Error('Make sure your internet connection is working!');
			}
		// We need to use a query on deviantART! First value is the referer!
		$POST = 'ref='.urlencode('https://'.$this->server['login']['host'].$this->server['login']['file']);
		// Second we have the username.
		$POST.= '&username='.urlencode($username);
		// Then the password.
		$POST.= '&password='.urlencode($pass);
		// And finally reusetoken... this means we don't get a new authtoken.
		$POST.= '&reusetoken=1' . chr(0);
		// And now we send our header and post data. First we declare the method as POST, select our file and protocol.
		fputs($socket, 'POST '.$this->server['login']['file'].' HTTP/1.1'.$this::LBR);
		// Now we determine the host URL. Don't ask why it's in this order.
		fputs($socket, 'Host: '.$this->server['login']['host'].$this::LBR);
		// The we need to determine what User-Agent is being used. This is a string contain information about the client.
		fputs($socket, 'User-Agent: '.$this->Agent.$this::LBR);
		// We only want to accept text and/or html in response to our query!
		fputs($socket, 'Accept: text/html'.$this::LBR);
		// This cookie tells our host to skip the intro...
		fputs($socket, 'Cookie: skipintro=1'.$this::LBR);
		// Finally we show the content type of the our data.
		fputs($socket, 'Content-Type: application/x-www-form-urlencoded'.$this::LBR);
		// Then the length of our query, and the query itself.
		fputs($socket, 'Content-Length: ' . strlen ($POST) . $this::LBR . $this::LBR . $POST);
		// Now that we have sent our data, we need to read the response. $response holds this data.
		$response = '';
		// The below loop actually reads the data from our socket.
		while (!feof ($socket))
			$response .= fgets ($socket, 500);
		// Now that we have our data, we can close the socket.
		fclose ($socket);
		// And now we do the normal stuff, like checking if the response was empty or not.
		if (!empty($response))
			{
			// Decode the returned data!
			$response = urldecode($response);
			// We need to find the cookie! So get rid of everything before it!
			$response = substr($response, strpos($response,'=')+1);
			// Now we have it from the first bit, crop it and unserialize it at the same time!
			$cookie = @unserialize(
				substr($response, 0, strpos($response, ';};')+2)
				);
			if ($cookie === false)
				{
				return null;
				}
			// Because errors still happen, we need to make sure we now have an array!
			if (is_array($cookie))
				{
				// Ok, it's an array, but does it contain the authtoken?
				if (array_key_exists('authtoken', $cookie)) return $cookie; // We got a valid cookie!
				}
			}
		// If we get here, then everything failed, so yeah... return null.
		return null;
		}
	
	public function connect()
		{
		// This method creates our dAmn connection!
		// First thing we do is create a socket stream using the server config data.
		$this->socket = @stream_socket_client('tcp://'.$this->server['chat']['host'].':'.$this->server['chat']['port']);
		// If we managed to open a connection, we need to do one or two things.
		if ($this->socket !== false)
			{
			// First we set the stream to non-blocking, so the bot doesn't pause when reading data.
			stream_set_blocking($this->socket, 0);
			// Now we make our handshake packet. Here we send information about the bot/client to the dAmn server.
			$data = 'dAmnClient '.$this->server['chat']['version'].$this::LBR;
			$data.= 'agent='.$this->Agent.$this::LBR;
			$data.= 'bot='.$this->Client.$this::LBR;
			$data.= 'owner='.$this->owner.$this::LBR;
			$data.= 'trigger='.$this->trigger.$this::LBR;
			$data.= 'creator=tristan/devTristan@gmail.com'.$this::LBR.chr(0);
			// This is were we actually send the packet! Quite simple really.
			@stream_socket_sendto($this->socket, $data);
			// Now we have to raise a flag! This tells everything that we are currently trying to connect through a handshake!
			$this->connecting = true;
			// Finally, exit before this if case exits, so we can do the stuff that happens when the socket stream fails.
			return true;
			}
		// All we do here is display an error message and return false dawg.
		return false;
		}

	public function login($username, $authtoken)
		{
		// Need to send a login packet? I'm your man!
		$this->login = ( $this->send("login $username\npk=$authtoken\n\0") ? true : true );
		}
	
	public function deform_chat($chat, $discard=false)
		{
		if (substr($chat, 0, 5)=="chat:")
			{
			return '#'.str_replace('chat:','',$chat);
			}
		if (substr($chat, 0, 6)=="pchat:")
			{
			if ($discard===false) {return $chat;}
			$chat = str_replace('pchat:','',$chat);
			$chat1 = substr($chat,0,strpos($chat,':'));
			$chat2 = substr($chat,strpos($chat,':')+1);
			$mod = true;
			if (strtolower($chat1) == strtolower($discard))
				{
				return '@'.$chat1;
				}
			else
				{
				return '@'.$chat2;
				}
			}
		return (substr($chat,0,1)=='#') ? $chat : (substr($chat, 0, 1)=='@' ? $chat : "#$chat");
		}

	public function format_chat($chat, $chat2=false)
		{
		$chat = str_replace('#','',$chat);
		if ($chat2!=false)
			{
			$chat = str_replace('@','',$chat);
			$chat2 = str_replace('@','',$chat2);
			if (strtolower($chat)!=strtolower($chat2))
				{
				$channel = 'pchat:';
				$users = array($chat, $chat2);
				sort($users);
				return $channel.$users[0].":".$users[1];
				}
			}
		return (substr($chat, 0, 5)=="chat:") ? $chat : (substr($chat, 0, 6)=="pchat:" ? $chat : 'chat:'.$chat);
		}
	
	public function join($channel) {$this->send("join $channel\n\0");}
	public function part($channel) {$this->send("part $channel\n\0");}
	public function say($ns, $message)
		{
		if (is_array($ns))
			{
			foreach($ns as $var1 => $var2)
				{
				$this->say(((is_string($var1)) ? $var1 : $var2), $message);
				}
			return;
			}
		$type = (substr($message, 0, 4)=="/me ") ? 'action' : ((substr($message, 0, 7)=="/npmsg ") ? 'npmsg' : 'msg');
		$message = ($type=='action') ? substr($message, 4) : ( ($type=='npmsg') ? substr($message, 7) : $message );
		$message = is_array($message) ? $message = '<bcode>'.print_r($message, true) : $message;
		$message = str_replace("&lt;",'<',$message);
		$message = str_replace("&gt;",'>',$message);
		$message = trim($message);
		$this->send("send $ns\n\n$type main\n\n$message\n\0");
		}
	public function action($ns, $message) {$this->say($ns, '/me '.$message);}
	public function npmsg($ns, $message) {$this->say($ns, '/npmsg '.$message);}
	public function promote($ns, $user, $pc=false) {$this->send("send $ns\n\npromote $user\n\n".($pc!=false?$pc:'').chr(0));}
	public function demote($ns, $user, $pc=false) {$this->send("send $ns\n\ndemote $user\n\n".($pc!=false?$pc:'').chr(0));}
	public function kick($ns, $user, $r=false) {$this->send("kick $ns\nu=$user\n".($r!=false?"\n$r\n":'').chr(0));}
	public function ban($ns, $user) {$this->send("send $ns\n\nban $user\n\0");}
	public function unban($ns, $user) {$this->send("send $ns\n\nunban $user\n\0");}
	public function get($ns, $property) {$this->send("get $ns\np=$property\n\0");}
	public function set($ns, $property, $value) {$this->send("set $ns\np=$property\n\n$value\n\0");}
	public function admin($ns, $command) {$this->send("send $ns\n\nadmin\n\n$command\0");}
	public function disconnect() {$this->send("disconnect\n\0");}
	public function send($data) {@stream_socket_sendto($this->socket, $data);}
	public function read()
		{
		$s = array($this->socket); $w = null;
		while (($s = @stream_select($s,$w,$w,60)) !== false)
			{
			if ($s === 0) {continue;}
			$data = @stream_socket_recvfrom($this->socket, 8192);
			if ($data !== false && $data !== '')
				{
				$this->buffer .= $data;
				$parts = explode(chr(0), $this->buffer);
				$this->buffer = ($parts[count($parts)-1] != '' ? $parts[count($parts)-1] : '');
				unset($parts[count($parts)-1]);
				if ($parts!==null) return $parts;
				return false;
				}
			else
				{
				return array("disconnect\ne=socket closed\n\n");
				}
			}
		return array("disconnect\ne=socket error\n\n");
		}
		
	/*
		===========================================================================
		FUNCTION: parse_tablumps
		----------------------------------------------------------------------------------------------------------------
		PARAMETERS:
			STRING $packet
		----------------------------------------------------------------------------------------------------------------
		RETURN VALUES:
			STRING $packet
		===========================================================================
			This function gets rid of all the nasty tablumps that comes with a packet. Srsly.
			This will only work with the dAmn class present. You can edit things if you
			know what you're doing.
		===========================================================================
	*/
	
	static public function parse_tablumps($data)
		{
		$data = str_replace(self::$tablumps['a1'], self::$tablumps['a2'], $data);
		$data = preg_replace(self::$tablumps['b1'], self::$tablumps['b2'], $data);
		return preg_replace("/<([^>]+) (width|height|title|alt)=\"\"([^>]*?)>/", "<\\1\\3>", $data);
		}

	/*
		===========================================================================
		FUNCTION: parse_dAmn_packet
		----------------------------------------------------------------------------------------------------------------
		PARAMETERS:
			STRING $packet
			STRING $separator
		----------------------------------------------------------------------------------------------------------------
		RETURN VALUES:
			ARRAY $packet
		===========================================================================
			This function splits dAmn packets into an array which can be easily processed.
			It requires function parse_tablumps() to work, but you can always comment that
			line out or replace it with your own function.
		===========================================================================
	*/

	static public function parse_dAmn_packet($data, $sep = '=')
		{
		$data = self::parse_tablumps($data);
		
		$packet = array(
			'cmd' => null,
			'param' => null,
			'args' => array(),
			'body' => null,
			'raw' => $data
			);
		if (stristr($data, "\n\n"))
			{
			$packet['body'] = trim(stristr($data, "\n\n"));
			$data = substr($data, 0, strpos($data, "\n\n"));
			}
		$data = explode("\n", $data);
		foreach($data as $id => $str)
			{
			if (strpos($str, $sep) != 0)
				{
				$packet['args'][substr($str, 0, strpos($str, $sep))] = substr($str, strpos($str, $sep)+1);
				}
			elseif (strlen($str) >= 1)
				{
				if (!stristr($str, ' '))
					{
					$packet['cmd'] = $str;
					}
				else
					{
					$packet['cmd'] = substr($str, 0, strpos($str, ' '));
					$packet['param'] = trim(stristr($str, ' '));
					}
				}
			}
		return $packet;
		}
}
