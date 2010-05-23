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
		if ($this->driver($element->tag))
			{
			$this->driver($element->tag)->parse($element);
			}
		}
	public function html($html)
		{
		$parts = explode('<',$html);
		header('Content-Type: text/plain');
		foreach ($parts as $id => $part)
			{
			if ($id == 0) {continue;}
			$part = '<'.$part;
			$tag_area = substr($part,0,strrpos($part,'>')+1);
			$tag_length = strlen($tag_area);
			$tag_name = substr($tag_area,1,strpos($tag_area,' ')-1);
			
			if (substr($tag_area,-2) == '/>')
				{
				$tag_type = 'selfclosing';
				}
			elseif (substr($tag_name,0,1) == '/')
				{
				$tag_type = 'closing';
				$tag_name = substr($tag_name,1);
				}
			else
				{
				$tag_type = 'opening';
				}
			$new_tag = '<'.(($tag_type == 'closing') ? '/' : '').$tag_name.(($tag_type == 'selfclosing') ? '/' : '').'>';
			$parts[$id] = $new_tag.substr($part,$tag_length);
			}
		echo "\n\n".implode('',$parts);
		die();
		$html = implode('<',$parts);
		}
}
