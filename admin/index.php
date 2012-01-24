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


//Set the Constant LABYRINTH
define("LABYRINTH_CONST", "LABYRINTH APPLICATION");

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
			if(addNewNode())
				json_encode(array("status"=>"961", "message"=>"Successfully Added"));
			else 
				json_encode(array("status"=>"971", "message"=>"Unable to add a new node"));
			break;
		case "removeNode":
			//delete question from the database and remove all paths attached to it
			if(removeNode())
				json_encode(array("status"=>"962", "message"=>"Successfully Added"));
			else 
				json_encode(array("status"=>"972", "message"=>"Unable to remove Node"));
			break;
		case "addPath":
			//get from and to data and create a new entry in the answer table
						
			if(addNewPath())
				json_encode(array("status"=>"963", "message"=>"Successfully Added"));
			else 
				json_encode(array("status"=>"973", "message"=>"Unable to add a new path"));
			break;
		case "removePath":
			//get from and to data and remove a path from the answer table
			if(removePath())
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
		<div class="outercontainer">
			<div class="buttons">
				<button id="addNode">Add New Node</button>
				<button id="removeNode">Remove Node</button>
				<button id="addPath">Add New Path</button>
				<button id="removePath">Remove Path</button>
			</div>
		</div>
		<script type="text/javascript" src="../template/jquery.min.js"></script>
		<script type="text/javascript" src="../template/jquery.form.js"></script>
		<script type="text/javascript" src="./admin.js"></script>
	</body>
</html>
<?php
