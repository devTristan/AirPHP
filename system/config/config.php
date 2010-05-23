<?php
$config = array(

'enabled' => true,
'debug_mode' => false,

'language' => 'english',
'charset' => 'UTF-8',

'host' => array(
	'domain' => 'localhost',
	'port' => 80,
	'protocol' => 'http',
	'basedir' => 'AirPHP/'
	),

'permitted_uri_chars' => 'a-z 0-9~%.:_\-',
'url_suffix' => '',

'cookies' => array(
	'prefix' => '',
	'domain' => 'localhost',
	'path' => '/'
	),

'routes' => s('config','routes'),

'mimes' => s('config','mimes'),
'error' => s('config','error'),

'autoload_folders' => array(),

'classtypes' => array(
	'codeigniter' => array(
		'prefix' => 'CI_',
		'is_compatibility' => true,
		'required[]' => 'CI',
		'autoload_folders' => array('libraries')
		),
	'controller' => array(
		'prefix' => 'controller_',
		'autoload_folders' => array('application/controllers')
		)
	),

'class_locations' => array(
	'CI' => 'system/compatibility/codeigniter'
	),

'codeigniter' => array(
	'time_reference' => 'local'
	),

'db' => s('config','db')

);
