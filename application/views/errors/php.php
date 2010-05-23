<html>
<head>
<title>PHP Error</title>
<style type="text/css">
<?php s('views')->include_view('airphp_style'); ?>
</style>
</head>
<body>
<h1>A PHP Error was encountered</h1>
<p>Severity: <?php echo s('airphp')->error_name($exception->getSeverity()); ?></p>
<p>Message: <?php echo $exception->getMessage(); ?></p>
<p>Filename: <?php echo $exception->getFile(); ?></p>
<p>Line Number: <?php echo $exception->getLine(); ?></p>
</body>
</html>
