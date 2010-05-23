<?php
class airphp {
private $error_level = 1;
public $error_levels = array(
	'default'		=> 'Error',
	E_ERROR			=> 'Error',
	E_WARNING		=> 'Warning',
	E_PARSE			=> 'Parsing Error',
	E_NOTICE		=> 'Notice',
	E_CORE_ERROR		=> 'Core Error',
	E_CORE_WARNING		=> 'Core Warning',
	E_COMPILE_ERROR		=> 'Compile Error',
	E_COMPILE_WARNING	=> 'Compile Warning',
	E_USER_ERROR		=> 'User Error',
	E_USER_WARNING		=> 'User Warning',
	E_USER_NOTICE		=> 'User Notice',
	E_STRICT		=> 'Runtime Notice'
	);
	public function __construct()
		{
		$this->define_constants();
		//set_error_handler(array($this,'error_handler'));
		//set_exception_handler(array($this,'exception_handler'));
		}
	public function error_name($level)
		{
		return (isset($this->error_levels[$level])) ? $this->error_levels[$level] : $this->error_levels['default'];
		}
	public function error_level($level = null)
		{
		if ($level === null) {return $this->error_level;} else {$this->error_level = $level;}
		}
	public function error_handler($severity, $message, $filename, $lineno)
		{
		if (!error_reporting() || $severity < $this->error_level) {return;}
		throw new ErrorException($message, 0, $severity, $filename, $lineno);
		}
	public function exception_handler($exception)
		{
		S('views')->show_view('errors/php',array(
			'exception' => $exception
			));
		}
	private function define_constants()
		{
		/*
		The following constants will be set, and here are some typical values:
			DIR_BASE: /var/www/
			DIR_APPLICATION: /var/www/application/
			DIR_CACHE: /var/www/cache/
			DIR_CONFIG: /var/www/config/
			DIR_HELPERS: /var/www/helpers/
			DIR_LIBRARIES: /var/www/libraries/
			DIR_DRIVERS: /var/www/libraries/drivers/
			DIR_LOGS: /var/www/logs/
			DIR_PUBLIC: /var/www/public/
			DIR_COMPATIBILITY: /var/www/compatibility/
			DIR_CONTROLLERS: /var/www/application/controllers/
			DIR_MODELS: /var/www/application/models/
			DIR_VIEWS: /var/www/application/views/
			DIR_ERRORS: /var/www/application/errors/
			URL: class/method/param1/param2
			URL_BASE: ../../../
		*/
		
		//EXT should be .php
		define('EXT',substr(__FILE__,strrpos(__FILE__,'.')));
		
		//publicdir: the directory of the public folder, where the entry point is
		$publicdir = substr($_SERVER['SCRIPT_FILENAME'],0,strrpos($_SERVER['SCRIPT_FILENAME'],'/'));
		//DIR_BASE: the absolute directory of the base of the framework.
		//something like /var/www/
		define('DIR_BASE',substr($publicdir,0,strrpos($publicdir,'/')).'/');
		
		//folders: the folders to be put into constants
		//DIR_APPLICATION should be something like /var/www/application/, and so on
		$folders = array(
			'system',
			'system/storage',
			'system/storage/cache',
			'system/config',
			'system/helpers',
			'system/libraries',
			'system/libraries/drivers',
			'system/logs',
			'system/compatibility',
			'application',
			'application/controllers',
			'application/models',
			'application/views',
			'application/errors',
			'public'
			);
		
		foreach ($folders as $folder)
			{
			$constant_name = end(explode('/',$folder));
			define('DIR_'.strtoupper($constant_name),DIR_BASE.$folder.'/');
			}
		
		//URL: everything in REQUEST_URI minus the basedir as defined in the configuration
		$url = substr($_SERVER['REQUEST_URI'],strlen(s('config')->host['basedir'])+1);
		if (substr_count($url,'?'))
			{
			$url = substr($url,0,strpos($url,'?'));
			}
		define('URL',$url);
		
		//URL_BASE: the relative path to the base directory for use in views
		//If the URL is "cake", URL_BASE will be "./". If the URL is "cake/14", URL_BASE will be ../
		//Mainly for use in views, eg "<img src="<?php echo URL_BASE.'myimg.png'"/>
		//variable substr_count used so that it doesn't have to be run twice
		$substr_count = substr_count(URL,'/');
		define('URL_BASE',($substr_count) ? str_repeat('../',$substr_count) : './');
		}
}
