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

ini_set("session.name","PHPSESSID");
ini_set("session.save_path", "/var/www/html/12/cms/uploads/sessions");

header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

session_start();
//TODO:

if(!isset($_SESSION['userId'])){
	header("Location: http://www.pragyan.org/12/events/brainwork/labyrinth/play+login");
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

if($userid == 0){
	echo "Some error occured with Pragyan Auth. Kindly report this issue to boopathi@live.com";
	exit(1);
}

//Includes
require_once("./config.inc.php");
require_once("./common.lib.php");

//declare global var
$CONTENT = "";

//connect to the database
connectDB();

//Check if the user has registered on the main site
function checkRegistrant($userid){
$rq = mysql_query("SELECT * FROM `pragyan12_cms`.`form_elementdata` WHERE `user_id`='{$userid}' AND `page_modulecomponentid`='34'") or die(mysql_error());
$rr = mysql_fetch_array($rq);// or die("MYSQL_ERROR:" . mysql_error());
if(empty($rr['form_elementdata'])):
	return false;
else:
	if($rr['form_elementdata'] === "Yes")
		return true;
	else
		return false;
endif;
}
if(checkRegistrant($userid) === false):
//	echo "Labyrinth is down for a moment. Kindly wait... More levels are being added. ";
	$CONTENT="It seems you have not registered on Labyrinth. Kindly register ";
	$CONTENT.="<a href='http://www.pragyan.org/12/home/events/brainwork/labyrinth/registrations/'>here</a>";
else:

$FORM = <<<FORM

\n\n\n
<div class="labyrinth_submit">
	<form name="labyrinth_submit" method="POST" action="$needle">
		<input type="text" name="labyrinth_answer" autocomplete="off"/>
		<input type="submit" value="Submit" />
	</form>
</div>
\n\n\n

FORM;

$PAGETITLE = "Labyrinth";

if(empty($answer)):

	//from the user's current level
	$userLevel = getUserCurrentLevel();
	//get the question for the userlevel
	$questionArray = getQuestion($userLevel);

	//get the user's last entered answer
	$lastEnteredAnswer = getUserLastAnswer();
	//if(empty($lastEnteredAnswer))
	//	$CONTENT = $questionArray['question'];
	//else
	header("Location: ./{$questionArray['url']}");
	$PAGETITLE = $questionArray['header'];
	$numberSolved = numberSolved($userLevel);

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
		$PAGETITLE = $questionArray['header'];
		$numberSolved = numberSolved($userLevel);
	} else 	if($userCurrentLevel == $requestLevelArray['from']){
		//then user entered a correct answer
		//$questionArray = getQuestion($requestLevelArray['to']);
		//$CONTENT = $questionArray['question'];
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
		$PAGETITLE = $questionArray['header'];
		$numberSolved = numberSolved($userLevel);
		updateAttempt($userCurrentLevel);
	}
	else {
		updateAttempt($userCurrentLevel);
		//user entered an answer that was not allowed
		$CONTENT = "You either entered a Wrong Answer or Tried to access some level that is restricted.";
		$userLevel = getQuestion(getUserCurrentLevel());
		$CONTENT .= "<br><br><a href=\"./{$userLevel['url']}\">Click here</a> to go to your current level.<br><br>";
		//and nullify the form so that he cannot submit it
		$PAGETITLE = "404";
		$FORM = "";
	}
endif;

endif;//end of checkRegistrant

require_once("./template/index.php");

?>
