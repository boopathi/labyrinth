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
	if(empty($userLevel)):
		//then try to find the starting level from the database
		$startNodeQuery = mysql_query("SELECT `value` FROM `config` WHERE `key`='start'") or die(mysql_error());
		$startNodeArray = mysql_fetch_assoc($startNodeQuery);
		if(empty($startNodeArray['value']))
			return 1;
		else
			return $startNodeArray['value'];
	endif;
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
function getNodes($key,$level){
	$key = escape($key);
	$level = escape($level);
	$fromtoQuery = mysql_query("SELECT * FROM `answers` WHERE `key`='{$key}' AND (`from`='{$level}' OR `to`='{$level}')") or die(mysql_error());
	$fromtoArray = mysql_fetch_assoc($fromtoQuery);
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
function addNewNode($questionHtml, $posx, $posy, $header){
	$questionHtml = escape($questionHtml);
	$posx = escape($posx);
	$posy = escape($posy);
	$header = escape($header);
	//$addNodeQuery = mysql_query("INSERT INTO `questions` (`question`) VALUES('{$questionHtml}')");
	//$addNodeQuery = mysql_query("INSERT INTO `questions` (`level`,`question`,`posX`,`posY`) SELECT MAX(`level`)+1  , '{$questionHtml}', '{$posx}', '{$posy}' FROM `questions`");
	$addNodeQuery = mysql_query("INSERT INTO `questions` (`question`,`header`,`posX`,`posY`) VALUES('{$questionHtml}','{$header}','$posx','$posy')");
	if($addNodeQuery) return true;
	else return false;
}

//Remove a Node(question) from the database
function removeNode($level) {
	$level = escape($level);
	$removeNodeQuery = mysql_query("DELETE FROM `labyrinth`.`questions` WHERE `questions`.`level` = '".$level."'");
	$removeNodeLinkPathsQuery = mysql_query("DELETE FROM `labyrinth`.`answers` WHERE `answers`.`from` = '".$level."' OR `answers`.`to` = '".$level."'");
	if($removeNodeQuery && $removeNodeLinkPathsQuery)return true;
	else return false;
}

//Add a new path(answer) in the database
function addNewPath($from, $to, $key){
	$from = escape($from); $to = escape($to); $key = escape($key);
	$addPathQuery = mysql_query("INSERT INTO `answers` (`from`,`to`,`key`) VALUES ('{$from}','{$to}','{$key}')");
	if($addPathQuery)
		return true;
	else
		return false;
}

//Remove a path(answers) from the database
function removePath($from, $to){
	$from = escape($from); $to = escape($to);
	$removePathQuery = mysql_query("DELETE FROM `labyrinth`.`answers` WHERE `answers`.`from` = '{$from}' AND `answers`.`to` = '{$to}'");
	if($removePathQuery) return true;
	else return false;
}

function getUserRequestLevel(){
	global $answer;
	$requestQuery = mysql_query("SELECT * FROM `answers` WHERE `key`='$answer' ");
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
    $from = escape($from); $to = escape($to);
	$requestQuery = mysql_query("SELECT * FROM `labyrinth`.`answers` WHERE `from` = '".$from."' AND `to` = '".$to."' LIMIT 1");
	if(mysql_num_rows($requestQuery)):
		$requestKey =  mysql_fetch_assoc($requestQuery);
		return $requestKey['key'];
	endif;
}

function initNodes(){
	$nodearray = array();
	$allNodes = mysql_query("SELECT * FROM `labyrinth`.`questions`") or die(mysql_error());

	if($allNodes):
		while($nodeinfo = mysql_fetch_assoc($allNodes)):
			$nodearray[] = array ("level"=>intval($nodeinfo['level']) , "posX"=>intval($nodeinfo['posX']) , "posY"=>intval($nodeinfo['posY']));
		endwhile;
		return $nodearray;
	endif;
	return false;
}

function initPaths(){
	$patharray = array();
	$allPaths = mysql_query("SELECT * FROM `labyrinth`.`answers`");
	if($allPaths):
		while($pathinfo = mysql_fetch_assoc($allPaths)):
			$patharray[] = array ("from"=>intval($pathinfo['from']) , "to"=>intval($pathinfo['to']) , "key"=>$pathinfo['key']);
		endwhile;
		return $patharray;
	endif;
	return false;
}


function getUserLastAnswer(){
	global $userid;
	$getUserLastAnsQuery = mysql_query("SELECT a.key FROM `answers` a JOIN `user_level` u on a.from=u.from AND a.to=u.to WHERE u.userid='{$userid} ORDER BY `id` DESC LIMIT 1' ") or die(mysql_error());
	if($getUserLastAnsQuery){
		$ans = mysql_fetch_assoc($getUserLastAnsQuery);
		return $ans['key'];
	}
}


function getLevelstats($from, $to){
	// no.of people who have solved a particular level..
	$noLevelSolved = mysql_query("SELECT DISTINCT `userid`,count(`from`) FROM `user_level` WHERE `from` = ".$from." LIMIT 1") or die(mysql_error());
	// no.of people who are currently in a particular level..
	$noInLevel = mysql_query("SELECT DISTINCT `userid`,count(`to`) FROM `user_level` WHERE `to` = ".$to." LIMIT 1") or die(mysql_error());
}

