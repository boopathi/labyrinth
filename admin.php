<?php

/* Project	: Labyrinth
 * Author 	: Boopathi Rajaa , Vignesh
 * Concept	: Matrix
 * Description:
 */


if(isset($_GET["_a"])){
//Set the Constant LABYRINTH
define("LABYRINTH_CONST", "BOOPATHI VIGNESH");
	include_once("config.inc.php");
	include_once("common.lib.php");
	connectDB();
	
	if(isset($_FILES['file'])){
		if((($_FILES['file']['type']=='image/gif')||($_FILES['file']['type']=='image/jpeg')||($_FILES['file']['type']=='image/pjpeg')||($_FILES['file']['type']=='image/png')||($_FILES['file']['type']=='image/x-png'))&&($_FILES['file']['size']<=(5*1024*1024)))
		{
			if($_FILES['file']['error']>0)
			echo "ERRORS FOUND<br/>".$_FILES['file']['error']."<br/>";
			else{
				if(file_exists("images/questions/".$_FILES['file']['name'])){
					echo "file already exists";
				}
				else {
					if(($_FILES['file']['type']=='image/jpeg')||($_FILES['file']['type']=='image/pjpeg')){
						$new_img=imagecreatefromjpeg($_FILES['file']['tmp_name']);
					}
					elseif (($_FILES['file']['type']=='image/gif')) {
						$new_img=imagecreatefromgif($_FILES['file']['tmp_name']);
					}
					elseif (($_FILES['file']['type']=='image/png')||($_FILES['file']['type']=='image/x-png')) {
						$new_img=imagecreatefrompng($_FILES['file']['tmp_name']);
					}
					
					list($width, $height) = getimagesize($_FILES['file']['tmp_name']);
					$imgratio=$width/$height;
					
					if ($imgratio>=(640/480) && $width >= 640){
						$newwidth = 640 ; 
						$newheight = 640/$imgratio; 
					}
					elseif ($imgratio >=(640/480) && $width < 640) {
						$newwidth = $width ; 
						$newheight = $height;
					}
					elseif ($imgratio <(640/480) && $height >= 480) {
						$newheight = 480;
						$newwidth = 480 * $imgratio ;
					}
					elseif ($imgratio <(640/480) && $height < 480) {
						$newheight = $height;
						$newwidth = $width ;
					}
					
					if (function_exists('imagecreatetruecolor')){ $resized_img = imagecreatetruecolor($newwidth,$newheight);}
					else { die("Error: Please make sure you have GD library ver 2+"); }
					
					imagecopyresampled($resized_img, $new_img, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
					//$resized_img = UnsharpMask($resized_img,80,0.5,3);
					ImageJpeg ($resized_img,"images/questions/".$_FILES['file']['name']);
					ImageDestroy ($resized_img);
					ImageDestroy ($new_img);
					
					move_uploaded_file( $_FILES['file']['tmp_name'] , "uploaded_files/original/".$_FILES['file']['name'] );
					echo "Successfully moved to http://localhost/images/questions/".$_FILES['file']['name'];
				}
			}
		}
	}
	
	if(isset($_POST['interface'])){
		if($_POST['interface']=='key'){
			$query = "INSERT INTO `labyrinth`.`answers` (`from`, `to`, `key`) VALUES ('".$_POST['from']."', '".$_POST['to']."', '".$_POST['input']."')";
			$result = mysql_query($query) or die(mysql_error());
			echo "key added successfully <br/><br/>";
			exit(0);
		}
		elseif ($_POST['interface']=='question') {
			$query = "INSERT INTO `labyrinth`.`questions` (`level`, `ans_type`, `answers`, `question`) VALUES ('".$_POST['node']."', 'POST', '4', '".$_POST['input']."')";
			$result = mysql_query($query) or die(mysql_error());
			echo "question added successfully <br/><br/>";
			exit(0);
		}
	}
	else {
		echo "Go away b******";
	}
exit(1);
}

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
	var paths = document.getElementsByClassName("path");
	var msg = document.getElementById("msg");
	var key_form = document.getElementById("key_form");
	var question_form = document.getElementById("question_form");
	var from , to , node ;
	
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
				from: from,
				to: to,
				node: node
			},
			onSuccess: function(data){
				msg.innerHTML = data+msg.innerHTML;
				console.log("done");
			},
			onError: function(data){
				msg.innerHTML = data+msg.innerHTML;
				console.log(data);
			}
		});
	}, false);

	for(i=0;i<points.length;i++){
		points[i].addEventListener("click", function(event){
			event.preventDefault();
			node = this.getAttribute("id");
			msg.innerHTML = "Node Selected:<br/>"+node+"<br/><br/>"+msg.innerHTML;
			//console.log("Node Selected:");
			//open form
			//console.log(node)
			inf.value = "question";
			key_form.style.display="none";
			question_form.style.display="block";
			//inp.focus();
		}, false);
	}
	
	for(i=0;i<paths.length;i++){
		paths[i].addEventListener("click",function(event){
			event.preventDefault();
			//console.log("Path Selected:");
			//open form
			var id = this.getAttribute("id").split('-');
			from = id[0] ; to = id[1];
			//console.log("from:"+from+" to:"+to);
			msg.innerHTML = "Path Selected:<br/>From: "+from+" To: "+to+"<br/><br/>"+msg.innerHTML;
			inf.value = "key";
			key_form.style.display="block";
			question_form.style.display="none";
			inp.focus();
		},false);
	}
}
</script>
<style type="text/css">
body{
	width:1080px;
	margin: auto;
}

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

#labyrinth_admin_form{
	position:fixed;
	left: 700px;
	top: 20px;
	width : 467px;
	height : 30 px;
	overflow : hidden;
	background: #aaa;
	font-size: 12px;
	padding: 20px;
	border-radius: 5px;
	-moz-border-radius: 5px;
	-webkit-border-radius: 5px;
}

#msg_board{
	height:430px;
	width: 499px;
	background: #aaa;
	position:fixed;
	left: 700px;
	top: 120px;
	font-size: 12px;
	padding:5px;
	border-radius: 5px;
	-moz-border-radius: 5px;
	-webkit-border-radius: 5px;
	overflow:scroll;
}

#msg{
	
}

#question_form{
	display:none;
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
					echo "<div class=\"node\" id=\"$node1\">".$node1."</div>";
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
<div id="msg_board">Messages:<br/><br/><div id="msg"></div></div>
<div id="labyrinth_admin_form">
	<div id="key_form">
	<label for="labyrinth_input">Enter value:</label>
	<input type="text" name="labyrinth_input" id="labyrinth_input" size="50"/>
	<input type="hidden" name="interface" value="" id="labyrinth_interface" />
	</div>
	<div id="question_form">
	<form action="#" method="post" enctype="multipart/form-data">
		<label for="file">File name :</label>
		<input type="file" name="file"/>
		<input type="submit" name="submit" value="Upload" />
	</form>
	</div>
</div>
</body>
</html>
