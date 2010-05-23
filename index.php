<?php
error_reporting(E_ALL);
$overhead_start = microtime(true);
define('FRAMEWORK_NAME','AirPHP');
define('FRAMEWORK_VERSION',0.7);
require_once 'system/core/autoload.php';
require_once 'system/core/functions.php';
require_once 'system/core/abstracts.php';
require_once 'system/core/airphp.php';
s('airphp');
s('config');
$overhead_end = microtime(true);
s('timing')->play('total')->set('total',$overhead_end-$overhead_start);
s('timing')->pause('overhead')->set('overhead',$overhead_end-$overhead_start);
unset($overhead_start);
unset($overhead_end);
if (!s('config')->enabled)
	{
	show_error(503);
	}
foreach (s('config')->autoload_classes as $class)
	{
	s($class);
	}
unset($class);
s('event')->trigger('initialize');

$class = s('router')->fetch_class();
$method = s('router')->fetch_method();
airphp_autoload('controller_'.$class);

if (s('router')->scaffolding_request === true) //this next bit happily borrowed from codeigniter and ported a bit
	{
	s($class)->_ci_scaffolding();
	}
else
	{
	if (method_exists(s('controller_'.$class), '_remap'))
		{
		s($class)->_remap($method);
		}
	else
		{
		if (!in_array(strtolower($method), array_map('strtolower', get_class_methods(s('controller_'.$class)))))
			{
			show_404($class.'/'.$method);
			}
		s('output')->start()->header('Content-Type','text/html');
		s('timing')->play('[controller] '.$class.'/'.$method);
		call_user_func_array(array(s('controller_'.$class), $method), array_slice(s('uri')->rsegments, 2));
		s('timing')->pause('[controller] '.$class.'/'.$method);
		s('output')->end();
		}
	}
die();
