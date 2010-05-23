<?php
class requestfiles extends classarray {
private $files = array();
	public function __construct()
		{
		$this->process_uploads();
		$this->set_array($this->files);
		}
	private function process_uploads()
		{
		foreach ($_FILES as $name => $file)
			{
			if (is_array($file['name']))
				{
				$this->files[$name] = array();
				foreach ($file['name'] as $id => $filename)
					{
					if ($filename != '')
						{
						$this->files[$name][] = new file($file['tmp_name'][$id],$filename);
						}
					}
				}
			else
				{
				$this->files[$name] = new file($file['tmp_name'],$file['name']);
				}
			}
		}
	public function __get($field)
		{
		return $this->files[$field];
		}
	public function __isset($field)
		{
		return isset($this->files[$field]);
		}
	public function limit($field,$max = 1)
		{
		return array_slice($this->files[$field],0,$max);
		}
	public function uploads()
		{
		return $this->files;
		}
}
