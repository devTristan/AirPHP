<html>
<head>
<title>Tests</title>
<link type="text/css" rel="stylesheet" href="<?php echo URL_BASE; ?>airphp.css" /> 
</head>
<body>
<h1>Tests</h1>
<ul>
<?php foreach ($pages as $page): ?>
	<li><a href="<?php echo $page['url'] ?>"><?php echo $page['name'] ?></a></li>
<?php endforeach; ?>
</ul>
</body>
</html>
