<?php
class parser_pwr_repeat {
	public function parse($element)
		{
		$code = '<?php $_i = 0; while ($_i < '.$element->times.'): $_i++; ?>';
		$code .= $element->innertext;
		$code .= '<?php endwhile; ?>';
		$element->outertext = $code;
		}
}
