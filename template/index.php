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
		<title>Labyrinth 3d</title>
		<script type="text/javascript">
			window.onload=function(){
			document.forms.labyrinth_submit.addEventListener("submit", function(evt){
			//evt.preventDefault();
				console.log("Default action prevented");
				}, true);
			}
		</script>
	</head>
	<body>
		<?php echo $userLevel; ?>
		<?php echo $CONTENT; ?>
		<?php echo $FORM; ?>
	</body>
</html>
