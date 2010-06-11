<?php
//This next bit happily borrowed from phpbb3
function deregister_globals()
	{
	$not_unset = array(
		'GLOBALS' => true,
		'_GET' => true,
		'_POST' => true,
		'_COOKIE' => true,
		'_REQUEST' => true,
		'_SERVER' => true,
		'_SESSION' => true,
		'_ENV' => true,
		'_FILES' => true
		);	
	//Not only will array_merge and array_keys give a warning if
	//a parameter is not an array, array_merge will actually fail.
	//So we check if _SESSION has been initialised.
	if (!isset($_SESSION) || !is_array($_SESSION))
		{
		$_SESSION = array();
		}
	//Merge all into one extremely huge array; unset this later
	$input = array_merge(
		array_keys($_GET),
		array_keys($_POST),
		array_keys($_COOKIE),
		array_keys($_SERVER),
		array_keys($_SESSION),
		array_keys($_ENV),
		array_keys($_FILES)
		);
	foreach ($input as $varname)
		{
		if (isset($not_unset[$varname]))
			{
			//Hacking attempt. No point in continuing unless it's a COOKIE
			if ($varname !== 'GLOBALS' || isset($_GET['GLOBALS']) || isset($_POST['GLOBALS']) || isset($_SERVER['GLOBALS']) || isset($_SESSION['GLOBALS']) || isset($_ENV['GLOBALS']) || isset($_FILES['GLOBALS']))
				{
				exit;
				}
			else
				{
				$cookie = &$_COOKIE;
				while (isset($cookie['GLOBALS']))
					{
					foreach ($cookie['GLOBALS'] as $registered_var => $value)
						{
						if (!isset($not_unset[$registered_var]))
							{
							unset($GLOBALS[$registered_var]);
							}
						}
					$cookie = &$cookie['GLOBALS'];
					}
				}
			}
		unset($GLOBALS[$varname]);
		}
	unset($input);
	}
deregister_globals();
