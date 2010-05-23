<?php
class parser_pwr_loop {
	public function parse($element)
		{
		$code = '<?php foreach('.$element->var.' as $_row): foreach ($_row as $_var => $_value) {$$_var = $_value;} unset($_var); unset($_value); ?>';
		$code .= $element->innertext;
		$code .= '<?php endforeach; ?>';
		$element->outertext = $code;
		}
}
