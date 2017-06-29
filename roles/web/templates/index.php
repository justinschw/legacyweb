<html>
<title>Legacy Web</title>
<body>
<?php
$redis = new Redis();
$redis->connect('127.0.0.1');
header("Location: " . $redis->get($_SERVER['HTTP_HOST']));
?>
</body>
</html>