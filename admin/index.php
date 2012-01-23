<?php

/* Project	: Labyrinth
 * Author 	: Boopathi Rajaa
 * Description:
 */


/*Statuses
 * 900 - No action specified ajax request failed
 * 901 - Unidentified Action Name (UAN)
 * 971 - Unable to add New Node
 * 972 - Unable to Remove a Node
 * 973 - Unable to add new Path
 * 974 - Unable to Remove Path 
**/

include("../common.lib.php");

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
							
							$questionHTML = "<img src='".$_SERVER['PHP_SELF']."/../../images/questions/".$_FILES['file']['name']."' />";
						}
					}
				}
			endif;		
			//get question details and update the database
			if($execute)
			if(addNewNode($questionHTML))
				json_encode(array("status"=>"961", "message"=>"Successfully Added"));
			else 
				json_encode(array("status"=>"971", "message"=>"Unable to add a new node"));
			break;
			
			
		case "removeNode":
			//delete question from the database and remove all paths attached to it
			if(removeNode(_POST('level')))
				json_encode(array("status"=>"962", "message"=>"Successfully Added"));
			else 
				json_encode(array("status"=>"972", "message"=>"Unable to remove Node"));
			break;
			
			
		case "addPath":
			//get from and to data and create a new entry in the answer table
						
			if(addNewPath( _POST('from') , _POST('to') , _POST('key') ))
				json_encode(array("status"=>"963", "message"=>"Successfully Added"));
			else 
				json_encode(array("status"=>"973", "message"=>"Unable to add a new path"));
			break;
			
			
		case "removePath":
			//get from and to data and remove a path from the answer table
			if(removePath( _POST('from') , _POST('to') ))
				json_encode(array("status"=>"964", "message"=>"Successfully Added"));
			else 
				json_encode(array("status"=>"974", "message"=>"Unable to remove Path"));
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
		
		<script type="text/javascript" src="./admin.js"></script>
	</body>
</html>
<?php
