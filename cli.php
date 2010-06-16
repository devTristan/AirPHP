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
error_reporting(E_ALL);
$olddir = getcwd();
chdir('../');
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
foreach (s('config')->autoload_classes as $class)
	{
	s($class);
	}
unset($class);
s('event')->trigger('initialize');
chdir($olddir);
