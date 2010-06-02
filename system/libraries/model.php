<?php
abstract class model extends base {
public $fields = array();
public $index = array();
public $defaults = array();
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
		return ''.$this->name();
		}
	final public function __call($method,$args)
		{
		//switch (str::beginswith($method,'validate_'))
		//	{
		//	case 'validate_': return array();
		//	case 'html_': return htmlspecialchars($args[0]);
		//	case /* KEEP GOING HERE */
		//	}
		}
	public function create()
		{
		$rows = array();
		foreach ($this->fields as $fieldname => $type)
			{
			if (!class_exists('field_'.$type[0],false))
				{
				require_once(DIR_DRIVERS.'model/fields/'.$type[0].'.php');
				}
			$field = s('field_'.$type[0]);
			$rows[] = $this->createrow(
				$fieldname,
				$field->type($type[0],$type[1]),
				$field->length($type[1]),
				$field->isnull($type[1]),
				$field->unsigned($type[1])
				);
			}
		foreach ($this->index as $type)
			{
			if (!class_exists('index_'.$type[0],false))
				{
				require_once(DIR_DRIVERS.'model/index/'.$type[0].'.php');
				}
			$index = s('index_'.$type[0]);
			$sql = $index->sql($type[1]);
			if ($sql)
				{
				$rows[] = $sql;
				}
			}
		$dbname = 'airphp';
		$sql =	'CREATE TABLE `'.$dbname.'`.`'.$this->tablename()."` (\n".$this->createrow('id','mediumint',8)." AUTO_INCREMENT PRIMARY KEY,\n";
		$sql .= implode(",\n", $rows);
		$sql .= "\n) ENGINE = MYISAM;";
		return $sql;
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
abstract class baseindex extends base {}
class index_unique extends baseindex {
	public function sql($args)
		{
		return 'UNIQUE KEY `'.implode(',',$args).'` (`'.implode('`, `',$args).'`)';
		}
}
class index_index extends baseindex {
	public function sql($args)
		{
		return 'KEY `'.implode(',',$args).'` (`'.implode('`, `',$args).'`)';
		}
}
class index_fulltext extends baseindex {
	public function sql($args)
		{
		return 'FULLTEXT (`'.implode('`, `',$args).'`)';
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
	/*final public function __unset($field)
		{
		return unset($this->data[$field]);
		}*/
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
