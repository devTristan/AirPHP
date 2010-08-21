<?php
abstract class model extends base {
public $fields = array();
public $index = array();
public $defaults = array();
public $engine = '';
public $database = null;
public $identifier;
private $rowclass;
private $name;
	public function name()
		{
		if (!isset($this->name))
			{
			$this->name = substr(get_class($this), 6);
			}
		return $this->name;
		}
	public function rowclass()
		{
		return 'model_row_'.$this->name();
		}
	public function __get($offset)
		{
		return $this->$offset();
		}
	public function tablename()
		{
		return s('config')->db->table_prefix.$this->name();
		}
	public function tablestr()
		{
		return '`'.$this->database.'`.`'.$this->tablename().'`';
		}
	public function get_one_by($field, $value)
		{
		$result = $this->select(array($field => $value), 1);
		return (isset($result[0])) ? $result[0] : null;
		}
	public function get_by($field, $value, $limit = null, $order = null)
		{
		$result = $this->select(array($field => $value), $limit, $order);
		return $result;
		}
	public function __call($method,$args)
		{
		if (str::beginswith($method,'get_one_by_'))
			{
			$field = substr($method,strlen('get_one_by_'));
			$value = $args[0];
			return $this->get_one_by($field,$value);
			}
		if (str::beginswith($method,'get_by_'))
			{
			$field = substr($method,strlen('get_by_'));
			$value = $args[0];
			$limit = isset($args[1]) ? $args[1] : null;
			$order = isset($args[2]) ? $args[2] : null;
			return $this->get_by($field, $value, $limit, $order);
			}
		}
	public function create()
		{
		$data = array();
		foreach ($this->fields as $fieldname => $type)
			{
			if ($type[0] == 'has_many') {continue;}
			if (!class_exists('field_'.$type[0],false))
				{
				require_once(DIR_DRIVERS.'model/fields/'.$type[0].'.php');
				}
			$field = s('field_'.$type[0]);
			$field_type = $field->type($type[1]);
			if (!$field_type) {$field_type = $type[0];}
			$newrow = array(
				'name' => $fieldname,
				'type' => $field_type,
				'args' => $field->args($type[1]),
				'null' => $field->isnull($type[1]),
				'unsigned' => $field->unsigned($type[1]),
				'auto_increment' => ($this->identifier == $fieldname)
				);
			if (isset($this->defaults[$fieldname]))
				{
				$newrow['default'] = $this->defaults[$fieldname];
				}
			$data['rows'][] = $newrow;
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
	public function select($where = null, $limit = null, $order = null)
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
	public function insert($data)
		{
		$sql = s('db')->build_insert(array(
			'db' => $this->database,
			'table' => $this->tablename(),
			'data' => $data
			));
		s('db')->query($sql);
		}
	public function add()
		{
		$rowclass = $this->rowclass();
		return new $rowclass($this);
		}
}
abstract class basefield extends base {
	public function type($args)
		{
		return false;
		}
	public function args($args)
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
	public function format($val)
		{
		return $val;
		}
	public function insert($args, $val)
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
		$this->seek($this->pointer);
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
				$rowclass = $this->model->rowclass;
				$this->data[] = new $rowclass($this->model, $row, $this);
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
public $model;
private $result, $previous_state, $data;
	public function __construct($model, $data = array(), &$result = null)
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
		foreach ($this->model->fields as $field => $field_data)
			{
			$value = isset($this->data[$field]) ? $this->data[$field] : $this->data[$this->model->identifier];
			$data[$field] = $this->field($field)->get($value, $this->model->fields[$field][1]);
			}
		return print_r($data, $echo);
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
	public function __isset($offset)
		{
		return $this->offsetExists($offset);
		}
	public function offsetGet($offset)
		{
		$value = isset($this->data[$offset]) ? $this->data[$offset] : $this->data[$this->model->identifier];
		return $this->field($offset)->get($value, $this->model->fields[$offset][1]);
		}
	public function __get($offset)
		{
		return $this->offsetGet($offset);
		}
	public function __call($offset, $args)
		{
		return $this->field($offset)->format($this->__get($offset), $this->model->fields[$offset][1], $args);
		}
	public function offsetSet($offset, $value)
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
	public function __set($offset, $value)
		{
		$this->offsetSet($offset, $value);
		}
	public function offsetUnset($offset)
		{
		unset($this->data[$offset]);
		}
	public function __unset($offset)
		{
		$this->offsetUnset($offset);
		}
	public function commit()
		{
		if ($this->result === null)
			{
			$data = array();
			foreach ($this->model->fields as $field => $field_data)
				{
				$value = isset($this->data[$field]) ? $this->data[$field] : null;
				$newval = $this->field($field)->insert($this->model->fields[$field][1], $value);
				if (isset($this->data[$field]) || $newval !== null)
					{
					$data[$field] = $newval;
					}
				}
			$this->model->insert($data);
			$this->result = true;
			}
		else if ($this->new_state)
			{
			$this->model->update($this->new_state,
				array($this->model->identifier => $this->previous_state[$this->model->identifier]),1);
			}
		$this->new_state = array();
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
