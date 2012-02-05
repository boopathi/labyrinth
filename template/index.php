<?php
if(!defined('LABYRINTH_CONST')):
	echo <<<ERROR
	<h1>Access Denied</h1>
	This vulnerable activity will be reported to the Administrator.
ERROR;
	exit(1);
endif;
?>
<html>
	<head>
		<title>Labyrinth</title>
	</head>
	<body>
		<?php echo $userLevel; ?>
		<?php echo $CONTENT; ?>
		<?php echo $FORM; ?>
	</body>
</html>
