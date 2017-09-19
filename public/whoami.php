<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="ROBOTS" content="noindex, nofollow, all"/>
</head>
<body>
<?php echo 'Your IP Address is: ' . $_SERVER[ 'REMOTE_ADDR' ];

if(isset($_GET[ 'phpinfo' ])){
	phpinfo();
}
?>
</body>
</html>

