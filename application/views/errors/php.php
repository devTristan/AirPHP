<html>
<head>
<title>PHP Error</title>
<link type="text/css" rel="stylesheet" href="<?php echo URL_BASE; ?>airphp.css" /> 
</head>
<body>
<h1>A PHP Error was encountered</h1>
<p>Severity: <?php echo s('airphp')->error_name($exception->getSeverity()); ?></p>
<p>Message: <?php echo $exception->getMessage(); ?></p>
<p>Filename: <?php echo $exception->getFile(); ?></p>
<p>Line Number: <?php echo $exception->getLine(); ?></p>
</body>
</html>
