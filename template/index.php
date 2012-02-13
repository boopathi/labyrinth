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

<div class="header">
	<h1><a href="http://www.pragyan.org/12/" target="_blank">Pragyan '12</a> > 
	<a href="http://www.pragyan.org/12/labyrinth/">Labyrinth</a> > <?php if(!empty($FORM))echo "Level " . ($userLevel - 1); ?></h1>
</div>

<div class="content">
<?php echo $CONTENT; ?>

</div>

<div class="answerform">
<?php echo $FORM; ?>
<p>Note: All answers are lower-case letters and numbers 0-9 with NO spaces or special characters.</p>
</div>

<div class="googleform">
<form method="get" action="http://www.google.com/search" target="_blank">
<input type="text"   name="q" size="31" maxlength="255" value="" />
<input type="submit" value="Google Search" />
</form>
</div>

</div>
</body>
</html>
