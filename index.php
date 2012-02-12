<?php

/* Project	: Labyrinth
 * Author 	: Boopathi Rajaa
 * Concept	: Matrix
 */

//Check if installation is done
$conf = include("./config.inc.php");
if($conf !== "LABYRINTH"):
	//then installation is not done
	header("Location: ./install.php");
	exit(1);
endif;
 
session_start();
//TODO:

if(!isset($_SESSION['userId'])){
	header("Location: http://www.pragyan.org/12/+login");
//	$_SESSION["userid"] = rand(5, 100);
}

//Set the Constant LABYRINTH
define("LABYRINTH_CONST", "LABYRINTH APPLICATION");


//Get the PATH
$needle = substr($_SERVER['SCRIPT_NAME'],0,strpos($_SERVER['SCRIPT_NAME'], "index.php"));
//$needle = strstr($_SERVER["SCRIPT_NAME"],"index.php", true);
$request = substr($_SERVER["REQUEST_URI"],strlen($needle));

//Required Details
$answer = "";
if(isset($_POST["labyrinth_answer"]))
	$answer = $_POST["labyrinth_answer"];
else
	$answer = $request;

$userid = $_SESSION["userId"];

//Includes
require_once("./config.inc.php");
require_once("./common.lib.php");

//declare global var
$CONTENT = "";

//connect to the database
connectDB();

$FORM = <<<FORM
	<div class="labyrinth_submit">
		<form name="labyrinth_submit" method="POST" action="$needle">
			<input type="text" name="labyrinth_answer" />
			<input type="submit" />
		</form>
	</div>
FORM;

if(empty($answer)):

	//from the user's current level
	$userLevel = getUserCurrentLevel();
	//get the question for the userlevel
	$questionArray = getQuestion($userLevel);

	//get the user's last entered answer
	$lastEnteredAnswer = getUserLastAnswer();
	if(empty($lastEnteredAnswer))
		$CONTENT = $questionArray['question'];
	else
		header("Location: ./{$questionArray['url']}");

else:
	//Check if the user has access to the particular level
	//$level = $request	
	$userLevel = $userCurrentLevel = getUserCurrentLevel();
	$requestLevelArray = getNodes($answer, $userCurrentLevel);
	
	//might be correct answer / trying to access a different level / the same level
	
	if($userCurrentLevel == isAccessingQuestion($answer)) {
	 	//then user is accessing the current level with the proper url
		$questionArray = getQuestion($userCurrentLevel);
		$CONTENT=$questionArray['question'];
	} else 	if($userCurrentLevel == $requestLevelArray['from']){
		//then user entered a correct answer
		$questionArray = getQuestion($requestLevelArray['to']);
		$CONTENT = $questionArray['question'];
		//update the database with the current level
		updateUserLevel($userCurrentLevel, $requestLevelArray['to']);
		header("Location: ./");
	}
	else if($userCurrentLevel == $requestLevelArray['to']) {
		//then user is trying to access the same level
		//$CONTENT = "You are still here";
		
		//from the user's current level
		$userLevel = getUserCurrentLevel();
		//get the question for the userlevel
		$questionArray = getQuestion($userLevel);
		$CONTENT = $questionArray['question'];
	}
	else if(false) {
	     
	}
	else {
		//user entered an answer that was not allowed
		$CONTENT = "Trying to access a restricted Level, this incident will be reported.";
		$userLevel = getQuestion(getUserCurrentLevel());
		$CONTENT .= "<a href=\"./{$userLevel['url']}\">Click here</a> to goto your level ";
		//and nullify the form so that he cannot submit it
		$FORM = "";
	}
endif;

require_once("./template/index.php");

?>