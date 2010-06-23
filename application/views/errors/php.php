<html>
<head>
<title>PHP Error</title>
<link type="text/css" rel="stylesheet" href="<?php echo URL_BASE; ?>airphp.css" /> 
</head>
<body>
<h1>A PHP Error was encountered</h1>
<p>Severity: <?php echo $severity; ?></p>
<p>Message: <?php echo $message; ?></p>
<p>Filename: <?php echo $file; ?></p>
<p>Line Number: <?php echo $line; ?></p>
<pre><?php print_r($backtrace); ?></pre>
</body>
</html>
