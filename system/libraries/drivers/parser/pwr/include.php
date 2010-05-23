<?php
class parser_pwr_include {
	public function parse($element)
		{
		$element->outertext = '<?php s(\'views\')->include_view(\''.$element->src.'\'); ?>';
		}
}
