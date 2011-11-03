<?php
/*Admin interface for labyrinth*/

session_start();

$userid = -1;
if(isset($_SESSION["user"]))
	$userid = $_SESSION["user"];

/*Configuration settings for number of levels*/

/*Initial setting*/
define("MATRIX_SIZE",3);

?>
<html>
<head><title>Labyrinth Admin</title>
<script type="text/javascript">
</script>
<style type="text/css">
.node{
	width: 50px;
	height: 50px;
	background: #56fe12;
}
.path{
	width: 50px;
	height: 25px;
}
.path-up,.path-left{
	background: #3c7ebd;
}
.path-down,.path-right{
	background: #feddee;
}
.path-left{
width:25px!important;
height: 50px!important;
float:left;
}
.path-right{
width:25px!important;
height:50px!important;
float:right;
}
</style>
</head>
<body>
<div class="leveledit">
<?php echo MATRIX_SIZE; ?>
<table border="1" cellspacing="0" cellpadding="0">
<tbody>
	<?php
	for($i=0;$i<=MATRIX_SIZE;$i++){
		echo "\n<tr>\n";
		for($j=0;$j<=MATRIX_SIZE;$j++){
			if($i%2 == 0){
				if($j%2 ==1) {
					echo "<td class=\"pathh-container path-container\">";
					echo <<<BOX2
					<div class="path-up path"></div>
					<div class="path-down path"></div>
BOX2;
				}
				else if($i != MATRIX_SIZE){
				  	echo "<td class=\"node-container\">"; 
					echo "<div class=\"node\"></div>";
				}
			}
			else {
				if($j%2==0){
					echo "<td class=\"pathv-container path-container\">";
					echo <<<BOX2
					<div class="path-left path"></div>
					<div class="path-right path"></div>
BOX2;
				}
				else if($j!=MATRIX_SIZE && $i != MATRIX_SIZE){
					echo "\n<td class=\"node-container\">";
					echo "<div class=\"node\"></div>";
				}
				else{
					echo "<td>";
				}
			}
			echo "\n</td>\n";
		}
		echo "\n</tr>\n";
	}
	?>
</tbody>
</table>
</div>
</body>
</html>
