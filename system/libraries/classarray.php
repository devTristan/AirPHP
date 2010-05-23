<?php
abstract class classarray extends library implements Iterator, Countable, ArrayAccess {
private $_array;
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
	public function offsetGet($offset)
		{
		return $this->_array[$offset];
		}
	public function offsetSet($offset,$value)
		{
		$this->_array[$offset] = $value;
		}
	public function offsetUnset($offset)
		{
		unset($this->_array[$offset]);
		}	
}
