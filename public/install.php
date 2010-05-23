<html>
<head>
<title>Install AirPHP</title>
<style type="text/css">
@font-face{font-family:"MuseoSans";src:url(public/fonts/MuseoSans.otf) format("opentype")}@font-face{font-family:"Monaco";src:url(public/fonts/Monaco.ttf) format("truetype")}body{background:#f5f7f7 url(public/line.png) repeat-y 50% 0;margin:40px;font-family:"MuseoSans","trebuchet ms",sans-serif;font-size:1em;color:#333}a{color:#039;background-color:transparent;font-weight:normal}h1{color:#333;background-color:transparent;border-bottom:1px solid #D0D0D0;font-size:2.5em;font-weight:bold;margin:24px 0 2px 0;padding:5px 0 6px 0;letter-spacing:-1.5px}code,pre{font-size:12px;background-color:#f9f9f9;border:1px solid #D0D0D0;color:#002166;display:block;margin:14px 0 14px 0;padding:12px 10px 8px;font-family:"Monaco",verdana,Monospace;font-size:0.7em}
input {
width: 200px;
background: #eee;
border: 1px solid #444;
padding: 3px;
border-radius: 4px;
outline: 0;
font-size: 0.9em;
}
input:hover {
background: #ddd;
}
input:focus {
background: #ccc;
}
#submitbutton {
width: auto;
display: inline;
}
form {
text-align: center;
}
</style>
</head>
<body>
<h1>Install AirPHP</h1>

<p>Thank you for choosing AirPHP!</p>

<?php
$error = $shell = array();
if (isset($_POST['username']) && isset($_POST['password']))
	{
	include('../system/helpers/str.php');
	$username = str::allow($_POST['username'],str::alphanumeric);
	if ($username != $_POST['username']) {$error[] = 'Usernames are alphanumeric.';}
	else
		{
		$password = sha1($_POST['password']);
		$filestr = $username."\n".$password;
		file_put_contents('../system/storage/cp_pwd',$filestr);
		$protocol = ($_SERVER['HTTPS']) ? 'https' : 'http';
		$domain = $_SERVER['SERVER_NAME'];
		$port = $_SERVER['SERVER_PORT'];
		$basedir = substr($_SERVER['REQUEST_URI'],1,-11);
		$config = file_get_contents('../system/config/config.php');
		$config = str_replace(
			"'host' => array(\n\t'domain' => 'localhost',\n\t'port' => 80,\n\t'protocol' => 'http',\n\t'basedir' => 'AirPHP/'\n\t),",
			"'host' => array(\n\t'domain' => '$domain',\n\t'port' => $port,\n\t'protocol' => '$protocol',\n\t'basedir' => '$basedir'\n\t),"
			,$config);
		file_put_contents('../system/config/config.php',$config);
		@mkdir('../system/storage/cache',0777,true);
		$error[] = 'Congratulations, AirPHP has been installed successfully! Now go delete public/install.php';
		}
	}
else
	{
	$dir = substr(__FILE__,0,-18);
	if (!is_writable('../system/storage/'))
		{
		$error[] = 'directory system/storage is not writable.';
		$shell[] = 'chmod 777 system/storage -R';
		}
	if (!is_writable('../system/config/config.php'))
		{
		$error[] = 'file system/config/config.php is not writable.';
		$shell[] = 'chmod 777 system/config/config.php';
		}
	if (version_compare(PHP_VERSION, '5.3.0') < 0)
		{
		$error[] = 'You must have PHP version 5.3.0 or higher. Your current version is '.PHP_VERSION.'.';
		}
	}

if ($error) {echo '<p>',implode('<br/>',$error),'</p>'; if ($shell) {echo '<code>cd ',$dir,'<br/>',implode('<br/>',$shell),'</code>';}} else { ?>
<p>Please choose a username and password. In future you will use them to access the control panel.</p>
<form action="install.php" method="post">
	<label for="username">Username:</label><br/><input type="text" id="username" name="username"/><br/>
	<label for="password">Password:</label><br/><input type="password" id="password" name="password"/><br/>
	<input type="submit" value="Install" id="submitbutton"/>
</form>
<?php } ?>
</body>
</html> 
