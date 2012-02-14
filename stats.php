<?php

/* Project	: Labyrinth
 * Author 	: Boopathi Rajaa
 * Description:
 */


/*Statuses
 * 600 - Operation completed Successflly
 * 601 - Some Error Occured
**/


//Set the Constant LABYRINTH
define("LABYRINTH_CONST", "LABYRINTH APPLICATION");

include("./config.inc.php");
include("./common.lib.php");

connectDB();		

//template_body
$TEMPLATE_BODY = "";


?>
<html>
	<head>
		<title>Labyrinth - Statistics</title>
	</head>
	<body>
<?php
/*
		<div class="outercontainer">
			<div class="graphcontainer">
				<canvas id="graph" width="940" height="400">
				</canvas>
			</div>
		</div>
		
		<div id="viewNode" style="position:absolute"></div>
*/
?>
		
		<script type="text/javascript" src="./template/jquery.min.js"></script>
<?php 
/*
		<script type="text/javascript" src="./template/jquery.form.js"></script>
		<script type="text/javascript" src="./template/ocanvas.min.js"></script>
*/ 
?>
	    	<script>
		    var stats_data = <?php echo getStats(); ?>	
		</script>
		<script type="text/javascript" src=""></script>
		<script type="text/javascript" src="./template/stats.js"></script>
	</body>
</html>
<?php
