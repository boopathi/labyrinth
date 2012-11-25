<?php

/*
 * Models.php
 * Contains the definition of super models and other models inherit from that
 */

namespace Model;

class User {
    private $userid;
    private $auth;
    private $level;
    private $question;
    public function __construct($userid) {
        $this->userid = $userid;
        $this->auth = false;
        //fetch initially the level that's stored in DB
        $this->level = getDBLevel();
        $this->question = getDBQuestion();
    }
    public function get($key) {
        switch($key) {
        case 'id':          return $this->userid;
        case 'level':       return $this->level;
        case 'question':    return $this->question;
        default:            return null;
        }
    }
    public function grant() {
        $this->auth = true;
    }
    private function getDBLevel() {
        $userAccess = mysql_query("SELECT `to` FROM `user_level` WHERE `userid`='{$this->userid}' ORDER BY `id` DESC LIMIT 1") or die(mysql_error());
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
    private function getDBQuestion() {
	    $questionQuery = mysql_query("SELECT `url`,`question`,`comments`,`header` FROM `questions` WHERE `level`='{$this->level}' LIMIT 1") or die(mysql_error());
	    $questionArray = mysql_fetch_assoc($questionQuery);
    	$question = $questionArray['question'] . "\n" . $questionArray['comments'];
    	return array(
    		"question"=>$question,
    		"header"=>$questionArray['header'],
    		"comments"=>stripslashes_deep($questionArray['comments']),
    		"url"=>$questionArray['url']
    	);
    }
}

