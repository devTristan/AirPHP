<?php
class structure extends library implements Iterator, Countable, ArrayAccess {
private $_array;
	public function __construct($array)
		{
		$this->set_array($array);
		}
	protected function set_array(&$array)
		{
		$this->_array = &$array;
		}
	public function rewind()
		{
		reset($this->_array);
		}
	public function current()
		{
		return current($this->_array);
		}
	public function key()
		{
		return key($this->_array);
		}
	public function next()
		{
		next($this->_array);
		}
	public function valid()
		{
		return (key($this->_array) !== null);
		}
	public function count()
		{
		return count($this->_array);
		}
	public function offsetExists($offset)
		{
		return isset($this->_array[$offset]);
		}
	public function __isset($offset) {return $this->offsetExists($offset);}
	public function offsetGet($offset)
		{
		return $this->_array[$offset];
		}
	public function __get($offset) {return $this->offsetGet($offset);}
	public function offsetSet($offset,$value)
		{
		$this->_array[$offset] = $value;
		}
	public function __set($offset, $value) {return $this->offsetSet($offset, $value);}
	public function offsetUnset($offset)
		{
		unset($this->_array[$offset]);
		}
	public function __unset($offset) {return $this->offsetUnset($offset);}
	public function _get_data()
		{
		return $this->_array;
		}
}
