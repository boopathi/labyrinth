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

ini_set("session.name","PHPSESSID");
ini_set("session.save_path", "/var/www/html/12/cms/uploads/sessions");

include("../config.inc.php");
include("../common.lib.php");

connectDB();

session_start();

if(!isset($_SESSION['userId']))
	header("Location: http://www.pragyan.org/12/+login");

$userid = $_SESSION['userId'];

if(isAdmin() !== true) {
	?>You are not an administrator. <a href="../">Click here to go back </a> <?php
	//header("Location: ../");
	exit(1);
}

//handle all ajax requests in the beginning
if(isset($_GET["_a"]) && _GET('_a') == 1) :
	if(!isset($_POST['action'])):
		echo json_encode(array("status"=>601,"message"=>"Unknown Ajax Request"));
		exit(1);
	endif;
	switch(_POST('action')){
		case "addNode":
			//upload images
			$execute = 1 ;
			$questionHTML = NULL ;
			$randomstring = randomStr();
			$questionHTML = NULL;
			
			//The classic file uploader script
			for($i=0 ; $i<count($_FILES['file']['name']) ; $i++){
				if(isset($_FILES['file'])):
					//check if there was any error
					if($_FILES['file']['error'][$i] > 0):
						echo json_encode(array("status"=>601, "message"=>$_FILES['file']['error'][$i]));
						continue;
						//exit(1);
					endif;
					
					//check if the file is an image
					if(strrpos($_FILES['file']['type'][$i], "image") !== false):
						$safename = $_FILES['file']['name'][$i];
						$safename = str_replace("#", "No.", $safename);
						$safename = str_replace("$", "Dollar.", $safename);
						$safename = str_replace("%", "percent", $safename);
						$safename = str_replace("^", "", $safename);
						$safename = str_replace("&", "and", $safename);
						$safename = str_replace("*", "", $safename);
						$safename = str_replace("?", "", $safename);
						//$safenameArr = explode(".", $safename);
						//$safename = $safenameArr[count($safenameArr)-2] . ".jpg";
						//just to make sure there are no overriddens
						if(file_exists($safename)):
							echo json_encode(array("status"=>601,"message"=>"File Already Exists"));
							exit(1);
						endif;
						
						//upload process
						if(($_FILES['file']['type'][$i]=='image/jpeg')||($_FILES['file']['type'][$i]=='image/pjpeg')){
							$new_img=imagecreatefromjpeg($_FILES['file']['tmp_name'][$i]);
						}
						elseif (($_FILES['file']['type'][$i]=='image/gif')) {
							$new_img=imagecreatefromgif($_FILES['file']['tmp_name'][$i]);
						}
						elseif (($_FILES['file']['type'][$i]=='image/png')||($_FILES['file']['type'][$i]=='image/x-png')) {
							$new_img=imagecreatefrompng($_FILES['file']['tmp_name'][$i]);
						}
				
						list($width, $height) = getimagesize($_FILES['file']['tmp_name'][$i]);
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
						
						
						if(!imagejpeg ($resized_img,"./../images/questions/".$safename)):
							echo json_encode(array("status"=>601,"message"=>"Image Write Error. Contact System Administrator."));
							continue;
							//exit(1);
						endif;
						
						imagedestroy ($resized_img);
						imagedestroy ($new_img);
						
						$questionHTML .= "<img src='".$_SERVER['PHP_SELF']."/../../images/questions/".$safename."' class='q_img'/>";
	
					else:
						echo json_encode(array("status"=>601,"message"=>"You can upload only Image files"));	
					endif;
				endif;
			}
					
			//get question details and update the database
			if($execute)
				if(addNewNode($questionHTML, _POST("posX"), _POST("posY"), _POST("header"), _POST("comments"), _POST("url") ))
					echo json_encode(array("status"=>600, 
					"message"=>"Successfully Added a new node",
					"posX"=>intval(_POST("posX")), 
					"posY"=>intval(_POST("posY")),
					"nodeId"=>intval(mysql_insert_id())
					));
				else 
					echo json_encode(array("status"=>601, "message"=>"Unable to add a new node"));
			else 
				echo json_encode(array("status"=>601, "message"=>"Unable to add , \$exec fail"));
			break;
			
		case "removeNode":
			//delete question from the database and remove all paths attached to it
			if(removeNode(_POST('level')))
				echo json_encode(array("status"=>600, "message"=>"Successfully Removed Node"));
			else 
				echo json_encode(array("status"=>601, "message"=>"Unable to remove Node"));
			break;
			
			
		case "addPath":
			//get from and to data and create a new entry in the answer table
						
			if(addNewPath( _POST('from') , _POST('to') , _POST('key') ))
				echo json_encode(array("status"=>600, "message"=>"Successfully Added Path"));
			else 
				echo json_encode(array("status"=>601, "message"=>"Unable to add a new path"));
			break;
			
			
		case "removePath":
			//get from and to data and remove a path from the answer table
			if(removePath( _POST('from') , _POST('to') ))
				echo json_encode(array("status"=>600, "message"=>"Successfully Added"));
			else 
				echo json_encode(array("status"=>601, "message"=>"Unable to remove Path"));
			break;
			
		case "showNode":
			$data = getQuestion(_POST('level'));
			if(!empty($data))
				echo json_encode(array("status"=>600, "message"=>"Successfully Fetched", "html"=>$data['question']));
			else
				echo json_encode(array("status"=>601, "message"=>"Query Failed"));
			break;
			
		case "showPath":
			$data = showPath(_POST('from') , _POST('to'));
			if(!empty($data))
				echo json_encode(array("status"=>600, "message"=>"Successfully Fetched", "html"=>$data));
			else
				echo json_encode(array("status"=>601, "message"=>"Query Failed"));
			break;	
			
		case "initGraph":
			$nodeInfo = initNodes();
			$pathInfo = initPaths();
			if( is_array($nodeInfo) && is_array($pathInfo) )
				echo json_encode(array("status"=>600, "message"=>"Successfully Fetched Info", "nodedata"=>$nodeInfo, "pathdata"=>$pathInfo));
			else
				echo json_encode(array("status"=>601, "message"=>"Unable to fetch info"));
			break;
			
		default:
			echo json_encode(array("status"=>601,"message"=>"Unidentified Action Name"));			
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
				<canvas id="graph" width="1000" height="600">
				</canvas>
			</div>
			<div class="forms">
				<form id="removeNode" action="./index.php?_a=1" method="POST">
					<input type="hidden" name="level" value=""  data-params="required" />
					<input type="hidden" name="action" value="removeNode" data-params="required" />
				</form>
				<form id="removePath" action="./index.php?_a=1" method="POST">
					<input type="hidden" name="from" value="" data-params="required" />
					<input type="hidden" name="to" value="" data-params="required" />
					<input type="hidden" name="action" value="removePath" />
				</form>
				<form id="addPath" action="./index.php?_a=1" method="POST">
					<input type="hidden" name="from" value=""  data-params="required"/><br/>
					<input type="hidden" name="to" value=""  data-params="required"/><br/>
					<input type="hidden" name="key" value=""  data-params="required"/>
					<input type="hidden" name="action" value="addPath" /><br/>
				</form>
			</div>
		</div>
		
		<div id="statusbar"></div>
		<div id="viewNode" class="floater" ></div>
		<div id="showTextBox" class="floater" ><input type="text" /></div>
		<div id="nodeheaderBox" class="floater" >
			<form id="addNode" action="./index.php?_a=1" method="POST" enctype="multipart/form-data">
					<input type="file" name="file[]" id="file" required="required" data-params="required" multiple/>
					<input type="hidden" name="action" value="addNode" data-params="required"/>
					<input type="hidden" name="posX" value="" data-params="required"/>
					<input type="hidden" name="posY" value="" data-params="required"/>
					<input type="text" name="header" value="" data-params="required"/>
					<textarea name="comments"></textarea>
					<textarea name="url"></textarea>
					<input type="submit" name="submit"/>
				</form>
		</div>
		
		<script type="text/javascript" src="../template/jquery.min.js"></script>
		<script type="text/javascript" src="../template/jquery.form.js"></script>
		<script type="text/javascript" src="../template/ocanvas.min.js"></script>
		<script type="text/javascript">
			var graph_data = <?php
					$nodeInfo = initNodes();
					$pathInfo = initPaths();	
					if( is_array($nodeInfo) && is_array($pathInfo) )
						echo json_encode(array("nodedata"=>$nodeInfo, "pathdata"=>$pathInfo));
			?>
		</script>
		<script type="text/javascript" src="./admin2.js"></script>
	</body>
</html>
<?php
