<?php
class parser extends library {
	public function parse($data,$filters,$ext = 'php')
		{
		foreach ($filters as $filter)
			{
			$data = $this->driver($filter)->$ext($data);
			}
		return $data;
		}
	public function parsefile($file,$filters = array())
		{
		if ($filters == array())
			{
			$filters = explode('.',substr($file,strrpos($file,'/')+1));
			array_shift($filters);
			$ext = array_pop($filters);
			}
		else
			{
			$ext = array_pop(explode('.',substr($file,strrpos($file,'/')+1)));
			}
		return (count($filters) == 0) ? false : $this->parse(file_get_contents($file),$filters,$ext);
		}
}
