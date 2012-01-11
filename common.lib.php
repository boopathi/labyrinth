<?php
 
/* Project	: Labyrinth
 * Author 	: Boopathi Rajaa , Vignesh
 * Concept	: Matrix
 * Description:
 */

if(!defined('LABYRINTH_CONST')):
	echo <<<ERROR
	<h1>Access Denied</h1>
	This vulnerable activity will be reported to the Administrator.
ERROR;
	exit(1);
endif;


function connectDB(){
	if(defined('DB_HOST')):
		$db = mysql_connect(DB_HOST, DB_USER, DB_PASS) or die(mysql_error());
		mysql_select_db(DB_NAME) or die(mysql_error());
		return true;
	endif;
	return false;
}

//to address from the last row of user_level table
function getUserCurrentLevel(){
	global $userid;
	$userAccess = mysql_query("SELECT `to` FROM `user_level` WHERE `userid`='$userid' ORDER BY `id` DESC LIMIT 1") or die(mysql_error());
	$userAccessArray = mysql_fetch_array($userAccess);
	$userLevel = $userAccessArray['to'];
	if(empty($userLevel))
		return 1;
	return $userLevel;
}

function getQuestion($userLevel) {
	$questionQuery = mysql_query("SELECT `question` FROM `questions` WHERE `level`='$userLevel' LIMIT 1") or die(mysql_error());
	$questionArray = mysql_fetch_array($questionQuery);
	$question = $questionArray['question'];
	return $question;
}

//get From and To in an array
function getNodes($key){
		$fromtoQuery = mysql_query("SELECT * FROM `answers` WHERE `key`='$key'") or die(mysql_error());
		$fromtoArray = mysql_fetch_array($fromtoQuery);
		$returnvalue = array(
			"from"	=> $fromtoarray['from'],
			"to"	=> $fromtoarray['to']
		);
		return $returnvalue;
}

function getUserRequestLevel(){
	global $answer;
	$requestQuery = mysql_query("SELECT * FROM `answers` WHERE `key`='$answer' ") or die(mysql_error());
	if(mysql_num_rows($requestQuery)){
		$requestQueryArray = mysql_fetch_array($requestQuery);
		//return info
		//add info to user_level table
	}
	else {
		//failed access
	}
}


