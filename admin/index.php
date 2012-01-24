<?php

/* Project	: Labyrinth
 * Author 	: Boopathi Rajaa
 * Concept	: Matrix
 * Description:
 */


/*Statuses
 * 900 - No action specified ajax request failed
 * 901 - Unidentified Action Name (UAN)
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
			//get question details and update the database
			break;
		case "removeNode":
			//delete question from the database and remove all paths attached to it
			break;
		case "addPath":
			//get from and to data and create a new entry in the answer table
						
			break;
		case "removePath":
			//get from and to data and remove a path from the answer table
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
