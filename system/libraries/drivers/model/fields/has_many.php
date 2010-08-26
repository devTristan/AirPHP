<?php
class field_has_many extends basefield {
	public function get($value, $args)
		{
		$model_name = $args[0];
		$model = s('models')->$model_name;
		return $model->get_by($args[1], $value);
		}
}
