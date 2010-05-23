<?php
class unit extends library {
private $active = true;
private $strict = false;
private $tests = array();
	public function active($active)
		{
		$this->active = $active;
		}
	public function use_strict($strict)
		{
		$this->strict = $strict;
		}
	private function run_nolog($args)
		{
		switch (count($args))
			{
			case 1: return ($args[0]) ? true : false;
			case 2: return ($this->strict) ? ($args[0] === $args[1]) : ($args[0] == $args[1]);
			}
		}
	public function run($dummy)
		{
		$args = func_get_args();
		if (isset($args[2]))
			{
			$test_name = array_pop($args);
			}
		else
			{
			$test_name = 'Nameless';
			}
		$result = $this->run_nolog($args);
		$this->tests[] = array($test_name,$result);
		return $result;
		}
	public function run_array($dummy)
		{
		$args = func_get_args();
		switch (count($args))
			{
			case 1:
				$result = true;
				foreach ($args[0] as $test){
					if (!$this->run_nolog(array($test)))
						{
						$result = false;
						break;
						}
					}
				break;
			case 3:
				$test_name = $args[2];
			case 2:
				$result = true;
				foreach ($args[0] as $test){
					if (!$this->run_nolog(array($test,$args[1])))
						{
						$result = false;
						break;
						}
					}
				break;
			}
		$this->tests[] = array($test_name,$result);
		return $result;
		}
	public function need($dummy)
		{
		$result = call_user_func_array(array($this,'run'),func_get_args());
		$data_name = (isset($args[1])) ? $args[1] : 'provided.';
		$test_name = (isset($args[2])) ? $args[2] : 'Nameless';
		if (!$result)
			{
			s('console')->fatal_error('Required unit test failed for '.$test_name);
			}
		}
	public function results()
		{
		return $this->tests;
		}
	public function report($delimiter = ', ')
		{
		foreach ($this->tests as $test)
			{
			$out[] = '<span style="color:#'.(($test[1]) ? '090' : '900').'">'.$test[0].'</span>';
			}
		return implode($out,$delimiter);
		}
}
