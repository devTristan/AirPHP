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
	'basedir' => 'photoapp/'
	),

'hash_salt' => '803fee1220904b10ba20f5fdfbf7c4553d21d857',

'permitted_uri_chars' => 'a-z 0-9~%.:_\-',
'url_suffix' => '',

'autoload_folders' => array(),

'classtypes' => array(
	'codeigniter' => array(
		'prefix' => 'CI_',
		'is_compatibility' => true,
		'required' => array('CI'),
		'autoload_folders' => array('libraries')
		),
	'controller' => array(
		'prefix' => 'controller_',
		'autoload_folders' => array('application/controllers')
		),
	'object' => array(
		'prefix' => 'obj_',
		'autoload_folders' => array('system/objects')
		),
	),

'class_locations' => array(
	'CI' => 'system/compatibility/codeigniter'
	),

'codeigniter' => array(
	'time_reference' => 'local'
	)

);
