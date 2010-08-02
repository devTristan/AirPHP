<?php
/*
@airdoc file
title: Main Controller
description:
	Builds the proper environment, ensures that input is valid, and ensures
	that the environment is sane. It then figures out which controller and
	method to use, checks if they exist (if not 404ing), and executes them.
*/
//error_reporting(E_ALL);
$old_dir = getcwd();
chdir(substr(__FILE__, 0, strrpos(__FILE__, '/')+1));

define('FRAMEWORK_NAME','AirPHP');
define('FRAMEWORK_VERSION',0.7);
require_once 'system/core/autoload.php';
require_once 'system/core/functions.php';
require_once 'system/core/abstracts.php';
require_once 'system/core/airphp.php';
s('airphp');
s('config');
foreach (s('config')->autoload_classes as $class)
	{
	s($class);
	}
unset($class);
s('event')->trigger('initialize');

chdir($old_dir);
unset($old_dir);
