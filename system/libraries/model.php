<?php
abstract class model extends base {
public $fields = array();
public $index = array();
public $defaults = array();
public $engine = '';
public $database = null;
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
	final public function __call($method,$args)
		{
		if (str::beginswith($method,'get_one_by_'))
			{
			$args = n('arr',$args)->addkeys(array('value'))->set('field',substr($method,strlen('get_one_by_')));
			$sql = 'SELECT * FROM '.$this->tablestr().' WHERE `'.$args['field'].'`='.$args['value'].' LIMIT 1';
			echo $sql;
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
				'unsigned' => $field->unsigned($type[1])
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
	public function select()
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
abstract class model_row extends structure {
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
class index extends field {}
