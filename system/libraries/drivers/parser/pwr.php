<?php
class parser_pwr extends driver {
	public function php($php)
		{
		$document = dom::str_get_html($php);
		$document->set_callback(array($this,'_callback'));
		return (string) $document;
		}
	public function _callback($element)
		{
		if (s('views')->view_exists('pwr/'.$element->tag))
			{
			s('views')->element = $element;
			//ob_start();
			//s('views')->include_view('pwr/'.$element->tag);
			$el = array();
			foreach ($element->attr as $attr => $value)
				{
				$el[$attr] = $value;
				}
			$el['tag'] = $element->tag;
			$el['innertext'] = $element->innertext;
			$el = '(object)'.var_export($el,true);
			$element->outertext = '<?php s(\'views\')->show_view(\'pwr/'.$element->tag.'\',array(\'element\' => '.$el.')); ?>';
			//ob_end_clean();
			return;
			}
		if ($this->driver($element->tag))
			{
			$this->driver($element->tag)->parse($element);
			}
		}
	public function html($html)
		{
		return $this->php($html);
		}
}
