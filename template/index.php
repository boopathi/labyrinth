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
<head><title><?php echo $PAGETITLE;?></title><link href="./template/style.css" type="text/css" rel="stylesheet" /></head>
<body>
<div class="outercontainer">

<div class="content">
<?php echo $CONTENT; ?>
</div>

<div class="answerform">
<?php echo $FORM; ?>
</div>

<div class="googleform">
<form method="get" action="http://www.google.com/search" target="_blank">
<input type="text"   name="q" size="31" maxlength="255" value="" />
<input type="submit" value="Google Search" />
</form>
</div>

</div>
<script type="text/javascript">
window.onload = function(){
document.onmousedown = function(e){ console.log(e.pageX + " " + e.pageY); };
}
</script>
</body>
</html>
