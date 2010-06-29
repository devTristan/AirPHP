<?php
class download extends helper {
	public static function file($path, $name = null)
		{
		if (func_num_args() == 1) {$name = substr(strrchr($path,'/'),1);}
		self::headers($name, filesize($path));
		readfile($path);
		}
	public static function data($name, $data)
		{
		self::headers($name, strlen($data));
		echo $data;
		}
	private static function headers($name, $size)
		{
		s('output')->cache(0);
		$ext = strtolower(substr(strrchr($name,'.'),1));
		$mime = (isset(s('config')->mimes[$ext])) ? s('config')->mimes[$ext] : 'application/octet-stream';
		s('output')->header('Content-Type', '"'.$mime.'"');
		s('output')->header('Content-Disposition', 'attachment; filename="'.$name.'"');
		s('output')->header('Expires:', '0');
		if (strstr($_SERVER['HTTP_USER_AGENT'], 'MSIE'))
			{
			s('output')->header('Cache-Control', 'must-revalidate, post-check=0, pre-check=0');
			}
		s('output')->header('Content-Transfer-Encoding', 'binary');
		s('output')->header('Pragma', (strstr($_SERVER['HTTP_USER_AGENT'], 'MSIE')) ? 'public' : 'no-cache');
		s('output')->header('Content-Length', $size);
		s('output')->flush()->end();
		}
}
