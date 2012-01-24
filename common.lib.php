<?php
 
/* Project	: Labyrinth
 * Author 	: Boopathi Rajaa
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

function escape($query)
{
        if (!get_magic_quotes_gpc()) {
            $xquery = mysql_real_escape_string($query);
            /// If there's no mysql connection, then the xquery will be false
            if($xquery===false)
            {
             connectDB();
             return escape($query);
            }
            else return $xquery;
        }
        return $query;
}

function _GET($key){
	return escape($_GET[$key]);
}

function _POST($key) {
	return escape($_POST[$key]);
}

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
	$userAccess = mysql_query("SELECT `to` FROM `user_level` WHERE `userid`='{$userid}' ORDER BY `id` DESC LIMIT 1") or die(mysql_error());
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
			"from"	=> $fromtoArray['from'],
			"to"	=> $fromtoArray['to']
		);
		return $returnvalue;
}

//Update the UserLevel in the database
function updateUserLevel($fromLevel, $toLevel){
	global $userid;
	$updateQuery = mysql_query("INSERT INTO `user_level` (`userid`, `from`, `to`) VALUES ('{$userid}','{$fromLevel}','{$toLevel}')") or die(mysql_erro());
	if($updateQuery)
		return true;
	else 
		return false;
}

//Add a new Node(question) in the database
function addNewNode($level, $questionHtml){
	$addNodeQuery = mysql_query("INSERT INTO `questions` (`level`,`question`) VALUE ('{$level}','{$questionHtml}')") or die(mysql_error());
	if($addNodeQuery) return true;
	else return false;
}

//Remove a Node(question) from the database
function removeNode($level) {
	$removeNodeQuery = mysql_query("DELETE FROM `labyrinth`.`questions` WHERE `questions`.`level` = '".$level."'");
	$removeNodeLinkPathsQuery = mysql_query("DELETE FROM `labyrinth`.`answers` WHERE `answers`.`from` = '".$level."' OR `answers`.`to` = '".$level."'");
}

//Add a new path(answer) in the database
function addNewPath($from, $to, $key){
	$addPathQuery = mysql_query("INSERT INTO `answers` (`from`,`to`,`key`) VALUES ('{$from}','{$to}','{$key}')") or die(mysql_error());
	if($addPathQuery)
		return true;
	else
		return false;
}

//Remove a path(answers) from the database
function removePath($from, $to){
	$removePathQuery = mysql_query("DELETE FROM `labyrinth`.`answers` WHERE `answers`.`from` = '{$from}' AND `answers`.`to` = '{$to}'") or die(mysql_error());
	if($removePathQuery) return true;
	else return false;
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


