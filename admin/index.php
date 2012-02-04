<?php

/* Project	: Labyrinth
 * Author 	: Boopathi Rajaa
 * Description:
 */


/*Statuses
 * 600 - Operation completed Successflly
 * 601 - Some Error Occured
 * 900 - No action specified ajax request failed
 * 901 - Unidentified Action Name (UAN)
 * 971 - Unable to add New Node
 * 972 - Unable to Remove a Node
 * 973 - Unable to add new Path
 * 974 - Unable to Remove Path 
**/


//Set the Constant LABYRINTH
define("LABYRINTH_CONST", "LABYRINTH APPLICATION");

include("../config.inc.php");
include("../common.lib.php");

connectDB();

//handle all ajax requests in the beginning
if(isset($_GET["_a"]) && _GET('_a') == 1) :
	if(!isset($_POST['action'])):
		echo json_encode(array("status"=>"900","message"=>"Unknown Ajax Request"));
		exit(1);
	endif;
	switch(_POST('action')){
		case "addNode":
			//upload images
			$execute = 1 ;
			$questionHTML = NULL ;
			
			//The classic file uploader script
			if(isset($_FILES['file'])):
				//check if there was any error
				if($_FILES['file']['error'] > 0):
					echo json_encode(array("status"=>601, "message"=>$_FILES['file']['error']));
					exit(1);
				endif;
				
				//check if the file is an image
				if(strrpos($_FILES['file']['type'], "image") !== false):
					$safename = $_FILES['file']['name'];
					$safename = str_replace("#", "No.", $safename);
					$safename = str_replace("$", "Dollar.", $safename);
					$safename = str_replace("%", "percent", $safename);
					$safename = str_replace("^", "", $safename);
					$safename = str_replace("&", "and", $safename);
					$safename = str_replace("*", "", $safename);
					$safename = str_replace("?", "", $safename);
					$safename = $safename . randomStr();
					//just to make sure there are no overriddens
					if(file_exists($safename)):
						echo json_encode(array("status"=>601,"message"=>"File Already Exists"));
						exit(1);
					endif;
				else:
					echo json_encode(array("status"=>601,"message"=>"You can upload only Image files"));	
				endif;
			endif;
			
			if(isset($_FILES['file'])):
				if((($_FILES['file']['type']=='image/gif')||($_FILES['file']['type']=='image/jpeg')||($_FILES['file']['type']=='image/pjpeg')||($_FILES['file']['type']=='image/png')||($_FILES['file']['type']=='image/x-png'))&&($_FILES['file']['size']<=(5*1024*1024)))
				{
					if($_FILES['file']['error']>0)	echo "ERRORS FOUND<br/>".$_FILES['file']['error']."<br/>";
					else{
						if(file_exists($_SERVER['PHP_SELF']."/../../images/questions/".$_FILES['file']['name'])){
							echo "file already exists";
							$execute = 0 ;
						}
						else {
							$type = NULL;
							if(($_FILES['file']['type']=='image/jpeg')||($_FILES['file']['type']=='image/pjpeg')){
								$new_img=imagecreatefromjpeg($_FILES['file']['tmp_name']); $type = ".jpeg";
							}
							elseif (($_FILES['file']['type']=='image/gif')) {
								$new_img=imagecreatefromgif($_FILES['file']['tmp_name']); $type = ".gif";
							}
							elseif (($_FILES['file']['type']=='image/png')||($_FILES['file']['type']=='image/x-png')) {
								$new_img=imagecreatefrompng($_FILES['file']['tmp_name']); $type = ".png";
							}
					
							list($width, $height) = getimagesize($_FILES['file']['tmp_name']);
							$imgratio = $width/$height;
					
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
							else { die("Error: Please make sure you have GD library ver 2+"); $execute = 0 ;}
					
							imagecopyresampled($resized_img, $new_img, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
							
							$new_name = randomStr();
							ImageJpeg ($resized_img,$_SERVER['PHP_SELF']."/../../images/questions/".$new_name.$type);
							ImageDestroy ($resized_img);
							ImageDestroy ($new_img);
							
							$questionHTML = "<img src='".$_SERVER['PHP_SELF']."/../../images/questions/".$new_name.$type."' />";
						}
					}
				}
			endif;		
			//get question details and update the database
			if($execute)
				if(addNewNode($questionHTML))
					echo json_encode(array("status"=>"961", "message"=>"Successfully Added"));
			else 
				echo json_encode(array("status"=>"971", "message"=>"Unable to add a new node"));
			break;
			
			
		case "removeNode":
			//delete question from the database and remove all paths attached to it
			if(removeNode(_POST('level')))
				echo json_encode(array("status"=>"962", "message"=>"Successfully Added"));
			else 
				echo json_encode(array("status"=>"972", "message"=>"Unable to remove Node"));
			break;
			
			
		case "addPath":
			//get from and to data and create a new entry in the answer table
						
			if(addNewPath( _POST('from') , _POST('to') , _POST('key') ))
				echo json_encode(array("status"=>"963", "message"=>"Successfully Added"));
			else 
				echo json_encode(array("status"=>"973", "message"=>"Unable to add a new path"));
			break;
			
			
		case "removePath":
			//get from and to data and remove a path from the answer table
			if(removePath( _POST('from') , _POST('to') ))
				echo json_encode(array("status"=>"964", "message"=>"Successfully Added"));
			else 
				echo json_encode(array("status"=>"974", "message"=>"Unable to remove Path"));
			break;
			
			
		default:
			echo json_encode(array("status"=>"901","message"=>"Unidentified Action Name"));			
	}
	exit(1);	
endif;

//template_body
$TEMPLATE_BODY = "";


?>
<html>
	<head>
		<title>Labyrinth - Administrator</title>
	</head>
	<link href="./admin.css" rel="stylesheet" type="text/css" />
	<body>
		<div class="outercontainer">
			<div class="graphcontainer">
				<canvas id="graph" width="940" height="400">
				</canvas>
			</div>
			<div class="forms">
				<form id="addNode" action="./index.php?_a=1" method="POST" enctype="multipart/form-data">
					<input type="file" name="file" required="required" data-params="required"/>
					<input type="hidden" name="action" value="addNode" data-params="required"/>
					<input type="submit" /> 
				</form>
				<form id="removeNode" action="./index.php?_a=1" method="POST">
					<input type="text" name="level" value=""  data-params="required" />
					<input type="hidden" name="action" value="removeNode" data-params="required" />
					<input type="submit" />
				</form>
				<form id="addPath" action="./index.php?_a=1" method="POST">
					<input type="text" name="from" value=""  data-params="required"/>
					<input type="text" name="to" value=""  data-params="required"/>
					<input type="text" name="key" value=""  data-params="required"/>
					<input type="hidden" name="action" value="addPath" />
					<input type="submit" />
				</form>
				<form id="removePath" action="./index.php?_a=1" method="POST">
					<input type="text" name="from" value="" data-params="required" />
					<input type="text" name="to" value="" data-params="required" />
					<input type="hidden" name="action" value="removePath" />
					<input type="submit" />
				</form>
			</div>
		</div>
		<div id="statusbar"></div>
		<script type="text/javascript" src="../template/jquery.min.js"></script>
		<script type="text/javascript" src="../template/jquery.form.js"></script>
		<script type="text/javascript" src="../template/ocanvas.min.js"></script>
		<script type="text/javascript" src="./admin.js"></script>
	</body>
</html>
<?php
