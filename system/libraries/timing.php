<?php
class timing extends library {
private $times = array();
	public function play($name)
		{
		if (isset($this->times[$name]))
			{
			if ($this->times[$name]['paused'] == true)
				{
				$this->times[$name]['starttime'] = microtime(true)-$this->elapsed($name);
				$this->times[$name]['paused'] = false;
				}
			}
		else
			{
			$this->times[$name] = array(
				'starttime'	=> microtime(true),
				'pausetime'	=> 0,
				'paused'	=> false
				);
			}
		return $this;
		}
	public function pause($name)
		{
		if (isset($this->times[$name]))
			{
			if ($this->times[$name]['paused'] == false)
				{
				$this->times[$name]['paused'] = true;
				$this->times[$name]['pausetime'] = microtime(true);
				}
			}
		else
			{
			$now = microtime(true);
			$this->times[$name] = array(
				'starttime'	=> $now,
				'pausetime'	=> $now,
				'paused'	=> true
				);
			}
		return $this;
		}
	public function elapsed($name)
		{
		if (!isset($this->times[$name]))
			{
			return null;
			}
		if ($this->times[$name]['paused'] == true)
			{
			$endtime = $this->times[$name]['pausetime'];
			}
		else
			{
			$endtime = microtime(true);
			}
		return $endtime-$this->times[$name]['starttime'];
		}
	public function set($name,$time)
		{
		if (isset($this->times[$name]))
			{
			if ($this->times[$name]['paused'] == true)
				{
				$this->times[$name]['starttime'] = $this->times[$name]['pausetime']-$time;
				}
			else
				{
				$this->times[$name]['starttime'] = microtime(true)-$time;
				}
			}
		return $this;
		}
	public function table()
		{
		$table = array();
		foreach ($this->times as $name => $data)
			{
			$table[] = array(
				'name' => $name,
				'time' => $this->elapsed($name)
				);
			}
		return $table;
		}
}
