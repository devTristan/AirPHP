<?php
s('views')->show_view('errors/php',array(
	'severity' => $severity,
	'message' => $message,
	'filepath' => $filepath,
	'line' => $line
	));
