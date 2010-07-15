<?php
abstract class model extends base {
public $fields = array();
public $index = array();
public $defaults = array();
public $engine = '';
public $database = null;
public $identifier;
private $name;
	public function name()
		{
		if (!isset($this->name))
			{
			$this->name = substr(get_class($this),6);
			}
		return $this->name;
		}
	public function tablename()
		{
		return s('config')->db->table_prefix.$this->name();
		}
	public function tablestr()
		{
		return '`'.$this->database.'`.`'.$this->tablename().'`';
		}
	public function get_one_by($field,$value)
		{
		$result = $this->select(array($field => $value),1);
		return (isset($result[0])) ? $result[0] : null;
		}
	public function __call($method,$args)
		{
		if (str::beginswith($method,'get_one_by_'))
			{
			$field = substr($method,strlen('get_one_by_'));
			$value = $args[0];
			return $this->get_one_by($field,$value);
			}
		}
	public function create()
		{
		$data = array();
		foreach ($this->fields as $fieldname => $type)
			{
			if (!class_exists('field_'.$type[0],false))
				{
				require_once(DIR_DRIVERS.'model/fields/'.$type[0].'.php');
				}
			$field = s('field_'.$type[0]);
			$data['rows'][] = array(
				'name' => $fieldname,
				'type' => $field->type($type[0],$type[1]),
				'length' => $field->length($type[1]),
				'null' => $field->isnull($type[1]),
				'unsigned' => $field->unsigned($type[1]),
				'auto_increment' => ($this->identifier == $fieldname)
				);
			}
		foreach ($this->index as $type)
			{
			$data['index'][] = $type;
			}
		$data['db'] = $this->database;
		$data['table'] = $this->tablename();
		$sql = s('db')->build_create($data);
		return $sql;
		}
	public function select($where = null,$limit = null,$order = null)
		{
		$sql = s('db')->build_select(array(
			'db' => $this->database,
			'table' => $this->tablename(),
			'where' => $where,
			'order' => $order,
			'limit' => $limit
			));
		$result = s('db')->query($sql);
		return new model_result($this,$result);
		}
	public function update($set = null,$where = null,$limit = null,$order = null)
		{
		$sql = s('db')->build_update(array(
			'db' => $this->database,
			'table' => $this->tablename(),
			'set' => $set,
			'where' => $where,
			'order' => $order,
			'limit' => $limit
			));
		s('db')->query($sql);
		}
	public function delete($where,$limit,$order)
		{
		$sql = s('db')->build_delete(array(
			'db' => $this->database,
			'table' => $this->tablename(),
			'where' => $where,
			'order' => $order,
			'limit' => $limit
			));
		s('db')->query($sql);
		}
	private function createrow($name, $type, $length = false, $null = false, $unsigned = false)
		{
		return '`'.$name.'` '.strtoupper($type).
			'('.(($length) ? $length : '').') '.
			(($unsigned) ? 'UNSIGNED ': '').
			(($null) ? 'NULL' : 'NOT NULL');
		}
	private function createrows($rows)
		{
		$result = array();
		foreach ($rows as $row)
			{
			$result[] = call_user_func_array(array($this,'createrow'),$row);
			}
		return $result;
		}
}
abstract class basefield extends base {
	public function type($type,$args)
		{
		return $type;
		}
	public function length($args)
		{
		return false;
		}
	public function isnull($args)
		{
		return false;
		}
	public function unsigned($args)
		{
		return false;
		}
	public function set($val)
		{
		return $val;
		}
	public function get($val)
		{
		return $val;
		}
}
class model_result extends base implements Iterator, Countable, ArrayAccess {
private $result;
private $pointer = 0;
private $data = array();
private $done = false;
private $model;
	public function __construct($model,$result)
		{
		$this->model = $model;
		$this->result = $result;
		}
	public function rewind()
		{
		$this->pointer = 0;
		}
	public function current()
		{
		$this->seek($this->pointer);
		return (isset($this->data[$this->pointer])) ? $this->data[$this->pointer] : null;
		}
	public function key()
		{
		return $this->pointer;
		}
	public function next()
		{
		$this->pointer++;
		}
	public function valid()
		{
		return isset($this->data[$this->pointer]);
		return count($this->data);
		}
	public function count()
		{
		$this->seek();
		return count($this->data);
		}
	public function offsetExists($offset)
		{
		$this->seek($offset);
		return isset($this->data[$offset]);
		}
	public function offsetGet($offset)
		{
		$this->seek($offset);
		return $this->data[$offset];
		}
	public function offsetSet($offset,$value)
		{
		$this->seek($offset);
		$this->model->update($value,array($this->model->identifier => $this->data[$offset][$this->model->identifier]),1);
		$this->data[$offset] = $value;
		}
	public function offsetUnset($offset)
		{
		$model->delete(array($this->model->identifier => $this->data[$offset][$this->model->identifier]));
		unset($this->data[$offset]);
		}
	private function seek($position = null)
		{
		while (!$this->done && (!isset($this->data[$this->pointer]) || $position === null))
			{
			if ($row = s('db')->fetch_assoc($this->result))
				{
				$this->data[] = new model_row($this->model,$row,$this);
				}
			else
				{
				$this->done = true;
				}
			}
		}
}
class model_row extends base implements Iterator, Countable, ArrayAccess {
protected $commit = false;
private $new_state = array();
private $model, $result, $previous_state, $data;
	public function __construct($model,$data,&$result = null)
		{
		$this->model = $model;
		$this->data = $data;
		$this->result = $result;
		if ($this->result !== null)
			{
			$this->previous_state = $this->data;
			}
		}
	public function print_r($echo = false)
		{
		$data = array();
		foreach ($this->data as $field => $value)
			{
			$data[$field] = $this->field($field)->get($value);
			}
		return print_r($data,$echo);
		}
	public function rewind()
		{
		reset($this->data);
		}
	public function current()
		{
		return current($this->data);
		}
	public function key()
		{
		return key($this->data);
		}
	public function next()
		{
		next($this->data);
		}
	public function valid()
		{
		return (key($this->data) !== null);
		}
	public function count()
		{
		return count($this->data);
		}
	public function offsetExists($offset)
		{
		return isset($this->data[$offset]);
		}
	public function offsetGet($offset)
		{
		return $this->field($offset)->get($this->data[$offset]);
		}
	public function offsetSet($offset,$value)
		{
		$value = $this->field($offset)->set($value);
		if ($this->result)
			{
			if ($this->previous_state[$offset] == $value)
				{
				unset($this->previous_state[$offset]);
				}
			else
				{
				$this->new_state[$offset] = $value;
				}
			}
		$this->data[$offset] = $value;
		}
	public function offsetUnset($offset)
		{
		unset($this->data[$offset]);
		}
	public function commit()
		{
		if ($this->new_state)
			{
			if ($this->result === null)
				{
				$this->model->insert($this->data);
				}
			else
				{
				$this->model->update($this->new_state,
					array($this->model->identifier => $this->previous_state[$this->model->identifier]),1);
				}
			}
		}
	private function field($field)
		{
		$field = $this->model->fields[$field][0];
		require_once(DIR_DRIVERS.'model/fields/'.$field.'.php');
		return s('field_'.$field);
		}
}
class field {
	static public function __callStatic($method,$args)
		{
		return array($method,$args);
		}
}
class index extends field {}
