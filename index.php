<?php

/* Project	: Labyrinth
 * Author 	: Boopathi Rajaa , Vignesh
 * Concept	: Matrix
 */

session_start();
//is this correct??
//generate a dynamic user id
$_SESSION["userid"] = 1;


//Set the Constant LABYRINTH
define("LABYRINTH_CONST", "BOOPATHI VIGNESH");

//Get the PATH
$needle = strstr($_SERVER["SCRIPT_NAME"],"index.php", true);
$request = substr($_SERVER["REQUEST_URI"],strlen($needle));

//Required Details
$answer = "";
if(isset($_POST["labyrinth_answer"]))
	$answer = $_POST["labyrinth_answer"];
else
	$answer = $request;

$userid = $_SESSION["userid"];

//Includes
require_once("./config.inc.php");
require_once("./common.lib.php");

//declare global var
$CONTENT = "";

//connect to the database
connectDB();

if(empty($answer)):
	//Get the user level from database

	//from the user's current level
	$userLevel = getUserCurrentLevel();
	//get the question for the userlevel
	$CONTENT = getQuestion($userLevel);
	
else:
	//Check if the user has access to the particular level
	//$level = $request
	
	$userCurrentLevel = getUserCurrentLevel();
	$requestLevelArray = getNodes($answer);
	
	//might be correct answer / trying to access a different level / the same level
	
	if($userCurrentLevel == $requestLevelArray['from']){
		//then user entered a correct answer
		$CONTENT = "Correct Answer";
	}
	else if($userCurrentLevel == $requestLevelArray['to']) {
		//then user is trying to access the same level
		$CONTENT = "You are still here";
	}
	else {
		//user entered an answer that was not allowed
		$CONTENT = "trying to access a different level";
	}
	
	//$CONTENT = "checking if the user has access to this particular level";
	
endif;


$FORM = <<<FORM
	<div class="labyrinth_submit">
		<form name="labyrinth_submit" method="POST" action="$needle">
			<input type="text" name="labyrinth_answer" />
			<input type="submit" />
		</form>
	</div>
FORM;

require_once("./template/index.php");

?>
