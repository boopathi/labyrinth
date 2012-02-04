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
	$userLevel = escape($userLevel);
	$questionQuery = mysql_query("SELECT `question`,`header` FROM `questions` WHERE `level`='$userLevel' LIMIT 1") or die(mysql_error());
	$questionArray = mysql_fetch_assoc($questionQuery);
	$question = $questionArray['question'];
	return array(
		"question"=>$question,
		"header"=>$questionArray['header']
	);
}

//get From and To in an array
function getNodes($key){
	$key = escape($key);
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
	$fromLevel = escape($fromLevel); $toLevel = escape($toLevel);
	$updateQuery = mysql_query("INSERT INTO `user_level` (`userid`, `from`, `to`) VALUES ('{$userid}','{$fromLevel}','{$toLevel}')") or die(mysql_erro());
	if($updateQuery)
		return true;
	else 
		return false;
}

//Add a new Node(question) in the database
function addNewNode($questionHtml, $posx, $posy){
	$questionHtml = escape($questionHtml);
	$posx = escape($posx);
	$posy = escape($posy);
	//$addNodeQuery = mysql_query("INSERT INTO `questions` (`question`) VALUES('{$questionHtml}')");
	//$addNodeQuery = mysql_query("INSERT INTO `questions` (`level`,`question`,`posX`,`posY`) SELECT MAX(`level`)+1  , '{$questionHtml}', '{$posx}', '{$posy}' FROM `questions`") or die(mysql_error());
	$addNodeQuery = mysql_query("INSERT INTO `questions` (`question`,`posX`,`posY`) VALUES('{$questionHtml}','$posx','$posy')") or die(mysql_error());
	if($addNodeQuery) return true;
	else return false;
}

//Remove a Node(question) from the database
function removeNode($level) {
	$level = escape($level);
	$removeNodeQuery = mysql_query("DELETE FROM `labyrinth`.`questions` WHERE `questions`.`level` = '".$level."'") or die(mysql_error());
	$removeNodeLinkPathsQuery = mysql_query("DELETE FROM `labyrinth`.`answers` WHERE `answers`.`from` = '".$level."' OR `answers`.`to` = '".$level."'") or die(mysql_error());
	if($removeNodeQuery && $removeNodeLinkPathsQuery)return true;
	else return false;
}

//Add a new path(answer) in the database
function addNewPath($from, $to, $key){
	$from = escape($from); $to = escape($to); $key = escape($key);
	$addPathQuery = mysql_query("INSERT INTO `answers` (`from`,`to`,`key`) VALUES ('{$from}','{$to}','{$key}')") or die(mysql_error());
	if($addPathQuery)
		return true;
	else
		return false;
}

//Remove a path(answers) from the database
function removePath($from, $to){
	$from = escape($from); $to = escape($to);
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

function randomStr($min_chars = 15, $max_chars = 15, $use_chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'){ 
	$num_chars  = rand($min_chars, $max_chars); 
	$num_usable = strlen($use_chars) - 1; 
	$string     = ''; 

	for($i = 0; $i < $num_chars; $i++){
		$rand_char = rand(0, $num_usable);
		$string .= $use_chars{$rand_char};
	}
	return $string;
}

function showPath( $from , $to){
	$requestQuery = mysql_query("SELECT * FROM `labyrinth`.`answers` WHERE `from` = '".$from."' AND `to` = '".$to."' LIMIT 1")or die (mysql_error());
	if(mysql_num_rows($requestQuery)):
		$requestKey =  mysql_fetch_assoc($requestQuery);
		return $requestKey['key'];
	endif;
}

function initNodes(){
	$nodearray = array();
	$allNodes = mysql_query("SELECT * FROM `labyrinth`.`questions`");
	if(mysql_num_rows($allNodes)>0):
		while($nodeinfo = mysql_fetch_assoc($allNodes)):
			$nodearray[] = array ("level"=>$nodeinfo['level'] , "posX"=>$nodeinfo['posX'] , "posY"=>$nodeinfo['posY']);
		endwhile;
		return $nodearray;
	endif;
	return FALSE;
}
