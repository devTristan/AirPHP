<?php
//works very quickly for any number of pages up to about 10,000,000,000,000,000
class pagination extends helper {
private static $padding_first = 2;
private static $padding_last = 2;
private static $padding_current = 2;
	public static function generate($total_rows, $rows_per_page, $current_row,
					$padding_first = null, $padding_last = null, $padding_current = null)
		{
		if ($padding_first === null) {$padding_first = self::$padding_first;}
		if ($padding_last === null) {$padding_last = self::$padding_last;}
		if ($padding_current === null) {$padding_current = self::$padding_current;}
		$page = floor($current_row/$rows_per_page)+1;
		$num_pages = ceil($total_rows/$rows_per_page)+1;
		$rows = array();
		
		$conditions = array(
			array(0, $padding_first),
			array(max($page-$padding_current-1,0), $page+$padding_current-1),
			array($num_pages-$padding_last-2, $num_pages)
			);
		foreach ($conditions as $condition)
			{
			for ($i = $condition[0]; $i <= $condition[1]; $i++)
				{
				$k = $i+1;
				if ($k < 1 || $k > $num_pages-1) {break;}
				if (!isset($rows[$k]))
					{
					$rows[$k] = array(
						'current' => ($k == $page),
						'page' => $k,
						'text' => $k
						);
					}
				}
			}
		$out = array();
		ksort($rows);
		$last = 0;
		foreach ($rows as $row)
			{
			if ($last+1 != $row['page'])
				{
				$out[] = array(
					'current' => false,
					'page' => false,
					'text' => '&#0133;'
					);
				}
			$out[] = $row;
			$last = $row['page'];
			}
		return $out;
		}
}
