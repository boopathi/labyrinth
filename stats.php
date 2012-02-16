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
	<table>
		<tbody>
<?php
function getAllStats($to){
	$to = escape($to);
	$qq = mysql_query("SELECT  cu.user_email, cu.user_fullname FROM pragyan12_laby.user_level lu, pragyan12_cms.pragyanV3_users cu WHERE lu.userid=cu.user_id AND lu.to='$to' ORDER BY lu.to desc") or die(mysql_error());
	$ret=array();
	while($qa = mysql_fetch_assoc($qq))
		$ret[]=$qa;
	return $ret;
}
$request_path = pathinfo($_SERVER['PHP_SELF']);
if($_GET['admin'] === '5'){
	$statsarr = getAllStats(intval(end($request_path))+2);
	for($i=0;$i<count($statsarr);$i++){
		$html = <<<ROW
<tr>
<td colspan="2">{$i}</td>
<td>{$statsarr[$i]["user_email"]}</td>
<td>{$statsarr[$i]["user_fullname"]}</td>
</tr>
ROW;
		echo $html;
	}
}
?>

	</tbody>
	</table>
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
