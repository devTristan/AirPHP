<?php
abstract class model extends base {
	final public function __call($method,$args)
		{
		switch (str::beginswith($method,'validate_'))
			{
			case 'validate_': return array();
			case 'html_': return htmlspecialchars($args[0]);
			case /* KEEP GOING HERE */
			}
		}
	public function create()
		{

		}
}
abstract class model_row extends classarray {
protected $commit = false;
private $insert = true;
private $data = array();
private $previous_state = array();
	final public function __construct()
		{
		$this->set_array($this->data);
		}
	final public function _already_inserted($state)
		{
		$this->insert = false;
		$this->previous_state = $this->data = $state;
		}
	final public function _not_inserted($state)
		{
		$this->previous_state = $this->data = $state;
		$this->data = $state;
		}
	final public function __set($field,$value)
		{
		$this->data[$field] = $value;
		}
	final public function __get($field)
		{
		return $this->data[$field];
		}
	final public function __isset($field)
		{
		return isset($this->data[$field]);
		}
	final public function __unset($field)
		{
		return unset($this->data[$field]);
		}
	final public function commit()
		{
		if ($this->data === $this->previous_state) {return true;}
		if ($this->insert)
			{

			}
		}
}
class field {
	static public function __callStatic($method,$args)
		{
		return array($method,$args);
		}
}
