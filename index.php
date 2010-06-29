<?php
/*
@airdoc file
title: Main Controller
description:
	Builds the proper environment, ensures that input is valid, and ensures
	that the environment is sane. It then figures out which controller and
	method to use, checks if they exist (if not 404ing), and executes them.
*/
$overhead_start = microtime(true);
$outputfile = 'system/storage/cache/output_'.sha1(array_shift(explode('?',$_SERVER['REQUEST_URI'])));
if (file_exists($outputfile))
	{
	$handle = fopen($outputfile, 'r');
	$expire = (int) fgets($handle);
	if ($expire < time())
		{
		fclose($handle);
		unlink($outputfile);
		}
	else
		{
		$headers = json_decode(fgets($handle), true);
		if ($headers['status'])
			{
			header($headers['status'][0], true, $headers['status'][1]);
			}
		foreach ($headers['normal'] as $header)
			{
			header($header, true);
			}
		fpassthru($handle);
		fclose($handle);
		die();
		}
	}
unset($outputfile);
error_reporting(E_ALL);
//This next bit happily borrowed from phpbb3
//If we are on PHP >= 6.0.0 we do not need some code
if (!version_compare(PHP_VERSION, '6.0.0-dev', '>='))
	{
	//If magic quotes is on, fix it
	if (function_exists('get_magic_quotes_gpc') && get_magic_quotes_gpc())
		{
		require_once('system/core/fixes/magic_quotes.php');
		}
	//If register globals is on, deregister them
	if (@ini_get('register_globals') == '1' || strtolower(@ini_get('register_globals')) == 'on' || !function_exists('ini_get'))
		{
		require_once 'system/core/fixes/register_globals.php';
		}
	}
define('FRAMEWORK_NAME','AirPHP');
define('FRAMEWORK_VERSION',0.7);
require_once 'system/core/autoload.php';
require_once 'system/core/functions.php';
require_once 'system/core/abstracts.php';
require_once 'system/core/airphp.php';
s('airphp');
s('config');
if (s('config')->host === false)
	{
	echo 'AirPHP hasn\'t yet been installed. You had better go to install.php';
	die();
	}
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

s('output')->start()->header('Content-Type','text/html');
s('timing')->play('[controller] '.$class.'/'.$method);
if (method_exists(s('controller_'.$class), '_remap'))
	{
	s($class)->_remap($method);
	}
else
	{
	if (!in_array($method, get_class_methods(s('controller_'.$class))))
		{
		show_404($class.'/'.$method);
		}
	call_user_func_array(array(s('controller_'.$class), $method), array_slice(s('uri')->rsegments, 2));
	}
s('timing')->pause('[controller] '.$class.'/'.$method);
s('output')->end();
