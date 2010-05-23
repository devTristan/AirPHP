<?php
class parser_min extends driver {
	public function css($code)
		{
		$code = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $code);
		$code = preg_replace('!:\s*(0|(?:(?:\d*\.?\d+(?:p(?:[xct])|(?:[cem])m|%|in|ex))))(\s+\1){1,3};!', ':$1;', $code);
		
		$code = preg_replace('!(outline|border)-style\s*:\s*(none|hidden|d(?:otted|ashed|ouble)|solid|groove|ridge|inset|outset )(?:\s+\2){1,3};!', '$1-style:$2;', $code);
		$code = preg_replace('!(outline|border)-style\s*:\s*((none|hidden|d(?:otted|ashed|ouble)|solid|groove|ridge|inset|outset )\s+(none|hidden|d(?:otted|ashed|ouble)|solid|groove|ridge|inset|outset ))(?:\s+\3)(?:\s+\4);!', '$1-style:$2;', $code);
		$code = preg_replace('!(outline|border)-style\s*:\s*((?:(?:none|hidden|d(?:otted|ashed|ouble)|solid|groove|ridge|inset|outset )\s+)?(none|hidden|d(?:otted|ashed|ouble)|solid|groove|ridge|inset|outset )\s+(?:none|hidden|d(?:otted|ashed|ouble)|solid|groove|ridge|inset|outset ))(?:\s+\3);!', '$1-style:$2;', $code);
		
		$code = preg_replace('!(outline|border)-color\s*:\s*((?:\#(?:[0-9A-F]{3}){1,2})|\S+)(?:\s+\2){1,3};!', '$1-color:$2;', $code);
		$code = preg_replace('!(outline|border)-color\s*:\s*(((?:\#(?:[0-9A-F]{3}){1,2})|\S+)\s+((?:\#(?:[0-9A-F]{3}){1,2})|\S+))(?:\s+\3)(?:\s+\4);!', '$1-color:$2;', $code);
		$code = preg_replace('!(outline|border)-color\s*:\s*((?:(?:(?:\#(?:[0-9A-F]{3}){1,2})|\S+)\s+)?((?:\#(?:[0-9A-F]{3}){1,2})|\S+)\s+(?:(?:\#(?:[0-9A-F]{3}){1,2})|\S+))(?:\s+\3);!', '$1-color:$2;', $code);
		
		$code = explode('#',$code);
		foreach ($code as $key => &$part)
			{
			if ($key != 0)
				{
				if (	substr($part,0,1) == substr($part,1,1) &&
					substr($part,2,1) == substr($part,3,1) &&
					substr($part,4,1) == substr($part,5,1))
					{
					$part = substr($part,0,1).substr($part,2,1).substr($part,4,1).substr($part,6);
					}
				}
			}
		$code = implode('#',$code);
		
		$code = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $code);
		$code = str_replace(array(': ',' :','; ',' ;',';}',' {','{ ',', '), array(':',':',';',';','}','{','{',','), $code);
		return $code;
		}
	public function js($code)
		{
		return $code;
		}
}
