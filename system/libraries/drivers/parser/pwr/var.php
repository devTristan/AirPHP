<?php
class parser_pwr_var {
	public function parse($element)
		{
		$element->outertext = '<?php echo '.$element->innertext.'; ?>';
		}
}
