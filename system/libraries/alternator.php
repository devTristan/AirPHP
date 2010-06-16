<?php
class alternator extends library {
private $position = 0;
private $num;
private $args;
	public function __construct($dummy1, $dummy2)
		{
		$this->args = func_get_args();
		$this->num = func_num_args();
		}
	public function reset()
		{
		$this->position = 0;
		}
	public function next()
		{
		$this->position = ($this->position+1 == $this->num) ? 0 : $this->position+1;
		}
	public function __toString()
		{
		return (string) $this->get();
		}
	public function get()
		{
		$current = $this->args[$this->position];
		$this->next();
		return $current;
		}
}
