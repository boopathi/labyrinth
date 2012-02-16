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
	<body style="text-align:center;background: black;color: white; font-family: sans-serif;"> 
	<h1>Labyrinth - Statistics</h1>
	<h4 style="text-align:left;margin: 0 auto; width: 900px;font-family: inherit;">
	x : Level Number <br/>
	y : Number Solved <br/>
	</h4>
	<div id="stats" style="width:900px; margin:auto; text-align:center;font-family: inherit">
		<img src="./template/loading.gif" id="loaderr"/>
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
			chart_data.push([stats_data[0][i].solved, '' + stats_data[0][i].level, '#'+random(6)]);
		}
		$("#loaderr").ready(function(){
			setTimeout(function(){
				$("#stats").html("").jqBarGraph({
					data: chart_data,
					//legend: true,
					width: "100%",
					height: $(window).height()-150,
					speed: 0.25,
					sort: 'desc'
				});
			}, 200);
		});
	});
	</script>
	</body>
</html>
<?php
