<?php
class views extends library {
private $data = array();
private $scope = false;
	public function view_exists($file)
		{
		$files = glob("application/views/$file.*");
		return ($files && isset($files[0]));
		}
	public function show_view($file,$data = array())
		{
		$this->data = $data;
		unset($data);
		$this->include_view($file,true);
		}
	public function __set($field, $value)
		{
		$this->data[$field] = $value;
		}
	public function __get($field)
		{
		return $this->data[$field];
		}
	public function include_view($___file,$___header = false)
		{
		$___folder = substr($___file,0,strrpos($___file,'/')).'/';
		$___prettyfolder = str_replace('/','-',$___folder);
		if ($___folder == '/') {$___folder = '';}
		$___files = glob("application/views/$___file.*");
		if (!$___files || !isset($___files[0]))
			{
			show_error('View not found: '.$___file);
			}
		$___file = substr($___files[0],strrpos($___files[0],'/')+1);
		$___viewfile = DIR_BASE.$___files[0];
		if (count(explode('.',$___file)) != 2)
			{
			if (!file_exists(DIR_CACHE.'view_'.$___prettyfolder.$___file) || filemtime(DIR_CACHE.'view_'.$___prettyfolder.$___file) <= filemtime($___viewfile))
				{
				$___parsed = s('parser')->parsefile($___viewfile);
				if ($___parsed !== false)
					{
					file_put_contents(DIR_CACHE.'view_'.$___prettyfolder.$___file,$___parsed);
					$___parsed = true;
					}
				}
			else
				{
				$___parsed = true;
				}
			}
		else
			{
			$___parsed = false;
			}
		foreach ($this->data as $___var => $___value)
			{
			$$___var = $___value;
			}
		unset($___var);
		unset($___value);
		if ($___header)
			{
			$___ext = substr($___file,strrpos($___file,'.')+1);
			s('output')->header('Content-Type',
				(isset(s('config')->mimes[$___ext]))
					? s('config')->mimes[$___ext]
					: s('config')->mimes['_default']);
			}
		unset($___header);
		include((($___parsed) ? DIR_CACHE.'view_'.$___prettyfolder.$___file : $___viewfile));
		}
}
