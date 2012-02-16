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
<head><title><?php echo $PAGETITLE;?></title><link href="./template/style.css" type="text/css" rel="stylesheet" />
<!--[if IE]>
<style type="text/css">
img {
width: expression( document.body.clientWidth > 399 ? "400px" : (document.body.clientWidth < 201 ? "200px" : auto ));
height: expression( document.body.clientHeight > 399 ? "400px" : (document.body.clientHeight < 201 ? "200px" : auto ));
}
</style>
<![endif]-->
</head>
<body>
<div class="outercontainer">

<div class="header">
	<h1><a href="http://www.pragyan.org/12/" target="_blank">Pragyan '12</a> > 
	<a href="http://www.pragyan.org/12/labyrinth/">Labyrinth</a> > <?php if(!empty($FORM))echo "Level " . ($userLevel - 1); ?></h1>

	<a href="http://www.pragyan.org/12/labyrinth/stats.php">Stats</a> | 
	<a href="http://www.pragyan.org/12/home/events/brainwork/labyrinth/discussion/" target="_blank">Forum</a> | 
	<iframe src="//www.facebook.com/plugins/like.php?href=https%3A%2F%2Fwww.facebook.com%2Flabyrinthpragyan&amp;send=false&amp;layout=button_count&amp;width=450&amp;show_faces=true&amp;action=like&amp;colorscheme=dark&amp;font=verdana&amp;height=21&amp;appId=224545730925393" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:90px; height:21px;position:relative;top:6px;padding: 0 10px" allowTransparency="true"></iframe> | 
	<a><?php echo $numberSolved; ?> solved this level</a>
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
