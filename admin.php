<?php
if(isset($_GET["_a"])):
echo "1";
exit(1);
endif;
/*Admin interface for labyrinth*/

session_start();

$userid = -1;
if(isset($_SESSION["user"]))
	$userid = $_SESSION["user"];

/*Configuration settings for number of levels*/

/*Initial setting*/
define("MATRIX_SIZE",20);

?>
<html>
<head><title>Labyrinth Admin</title>
<script type="text/javascript" src="./template/ajax.lib.js" ></script>
<script type="text/javascript">
window.onload = function(event){
	var points = document.getElementsByClassName("node");
	
	var inp = document.getElementById("labyrinth_input");
	var inf = document.getElementById("labyrinth_interface");
	
	inp.addEventListener("keydown", function(event){
		if(event.keyCode != 13)
			return;
		event.preventDefault();
		//ajax call
		ajax({
			url:"./admin.php?_a=1",
			type: "POST",
			data: {
				input: inp.value,
				"interface": inf.value,
				from: "",
				to: ""

			},
			onSuccess: function(data){
				//done
				console.log("done");
			},
			onError: function(data){
				console.log(data);
			}
		});
	}, false);

	for(i=0;i<points.length;i++){
		points[i].addEventListener("click", function(event){
			event.preventDefault();
			console.log("came here");
			//open form
			inf.value = "question";
			inp.focus();
		}, false);
	}
}
</script>
<style type="text/css">
.node{
	width: 25px;
	height: 25px;
	background: #56fe12;
	font-size: 10px;
}
.path{
	width: 25px;
	height: 12px;
	font-size: 8px;
}
.path-up,.path-left{
	background: #3c7ebd;
}
.path-down,.path-right{
	background: #feddee;
}
.path-left{
width:12px!important;
height: 25px!important;
float:left;
}
.path-right{
width:12px!important;
height:25px!important;
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
	for($i=0;$i<MATRIX_SIZE;$i++){
		echo "\n<tr>\n";
		for($j=0;$j<MATRIX_SIZE;$j++){
			if($i%2 == 0){
				if($j%2 ==1) {
					$node1 = (MATRIX_SIZE*$i/4)+($j-1)/2;
					$node2 = (MATRIX_SIZE*$i/4)+($j+1)/2;
					if($j==MATRIX_SIZE-1)
					$node2 = (MATRIX_SIZE*$i/4);
					echo "<td class=\"pathh-container path-container\">";
					echo <<<BOX2
					<div class="path-up path" id="$node1-$node2">$node1-$node2</div>
					<div class="path-down path" id="$node2-$node1">$node2-$node1</div>
BOX2;
				}
				else if($j != MATRIX_SIZE){
					$node1 = (MATRIX_SIZE*$i/4)+($j)/2;
				  	echo "<td class=\"node-container\">"; 
					echo "<div class=\"node\" id=\".$node1.\">".$node1."</div>";
				}
				else {
					echo "<td>";
				}
			}
			else {
				if($j%2==0){
					$node1 = ($i-1)*MATRIX_SIZE/4+$j/2;
					$node2 = ($i+1)*MATRIX_SIZE/4+$j/2;
					if($i==MATRIX_SIZE-1)
					$node2 = $j/2;
 					echo "<td class=\"pathv-container path-container\">";
					echo <<<BOX2
					<div class="path-left path" id="$node1-$node2">$node1-$node2</div>
					<div class="path-right path" id="$node2-$node1">$node2-$node1</div>
BOX2;
				}
				/*else if($j!=MATRIX_SIZE && $i != MATRIX_SIZE){
					echo "\n<td class=\"node-container\">";
					echo "<div class=\"node\"></div>";
				}*/
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
<div id="labyrinth_admin_form">
	<input type="text" name="labyrinth_input" id="labyrinth_input" size="50"/>
	<input type="hidden" name="interface" value="" id="labyrinth_interface" />
</div>
</body>
</html>
