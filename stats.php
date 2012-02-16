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
	<body style="text-align:center"> 
	<h1>Labyrinth - Statistics | Number Completed</h1>
	<div id="stats" style="width:900px; margin:auto; text-align:center;">
	</div> 
	<script type="text/javascript" src="./template/jquery.min.js"></script>
	<script>
	    var stats_data = <?php echo getStats(); ?>	
	</script>
	<script type="text/javascript" src="./template/jquery.bar.js" ></script>
	<script type="text/javascript" >
	function random(l){
		var allowed="0123456789abcdef";
		var str="";
		for(var i=0;i<l;i++){
			str = str + (allowed[Math.round(Math.random()*allowed.length-1)] || 'f');
		}
		return str;
	}

	$(function(){
		var chart_data = [];
		for(i in stats_data[0]){
			console.log(random(6));
			chart_data.push([stats_data[0][i].solved, '' + stats_data[0][i].level, '#'+random(6)]);
		}
		$("#stats").jqBarGraph({
			data: chart_data,
			//legend: true,
			width: "100%",
			speed: 0.25
		});
		$("#graphBarStats").css("display","block");
	});
	</script>
	</body>
</html>
<?php
