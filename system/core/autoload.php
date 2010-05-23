<?php
function airphp_autoload($class)
	{
	$checked = array();
	$folders = array('system/libraries','system/helpers');
	if (defined('CONFIG_LOADED'))
		{
		$folders = (s('config')->autoload_folders) ? array_merge($folders,s('config')->autoload_folders) : $folders;
		if (defined('DIR_BASE'))
			{
			foreach ($folders as $key => $folder)
				{
				$folders[$key] = DIR_BASE.$folder;
				}
			}
		$predefined = (s('config')->class_locations) ? s('config')->class_locations : array();
		if (isset($predefined[$class]))
			{
			$folders = array(((defined('DIR_BASE') ? DIR_BASE : '')).$predefined[$class]);
			}
		else
			{
			$type = classmanager::class_type($class);
			if (isset(s('config')->classtypes[$type]))
				{
				$prefix = classmanager::class_prefix($class);
				$is_compat = classmanager::class_is_compatibility($class);
				$file_noprefix = substr($class,strlen($prefix));
				if (isset(s('config')->classtypes[$type]['required']))
					{
					foreach (s('config')->classtypes[$type]['required'] as $needed)
						{
						airphp_autoload($needed);
						}
					}
				if (isset(s('config')->classtypes[$type]['autoload_folders']))
					{
					$folders = array();
					if ($is_compat)
						{
						foreach (s('config')->classtypes[$type]['autoload_folders'] as $folder)
							{
							$folders[] = DIR_COMPATIBILITY.$type.'/'.$folder;
							}
						}
					else
						{
						foreach (s('config')->classtypes[$type]['autoload_folders'] as $folder)
							{
							$folders[] = DIR_BASE.$folder;
							}
						}
					}
				}
			}
		}
	foreach ($folders as $folder)
		{
		$file = $folder.'/'.((isset($file_noprefix)) ? $file_noprefix : $class).'.php';
		$checked[] = $file;
		if (file_exists($file))
			{
			require_once($file);
			break;
			}
		}
	if (!class_exists($class))
		{
		$str = '';
		$str .= "Couldn't find class $class<br/>\n";
		$str .= "Checked the following locations:<br/>\n";
		$str .= "<ol>\n";
		foreach ($checked as $file)
			{
			$str .= "<li>$file</li>\n";
			}
		$str .= "</ol>";
		show_error($str);
		}
	}
spl_autoload_register('airphp_autoload');
