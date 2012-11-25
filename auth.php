<?php

/*
 * Authentication part.
 * A function auth - returns true if authentication was successfull
 * if not, then function error will be called.
 */

require_once('model.lib.php');
namespace Auth {
    function authenticate() {
        
    }
};

class Authenticate {
    public var $userid;
    function __construct() {
        $this->userid = null;
        //call the authenticate method that returns the authenticate variable;
        $this->authenticate = $this->authenticate(); //true or false only
        if($this->authenticate == false)
            $this->error();
    }
    function authenticate() {
        //Write your authenticate method here
        //Return true if authenticated. Note: It should be strictly true

        //For PragyanCMS authentication
        return this->pragyanAuth();
    }
    public function error() {
        //This function ll be the callback when authenticate returns false.
        //You may exit the program here. Not exiting is no harm either.
        echo "Authentication Failure.";
        return false;
    }
    function pragyanAuth() {
        ini_set("session.name", "PHPSESSID");
        ini_set("session.save_path", "/home/boopathi/tmp/sessions");

        session_start();

        if(!isset($_SESSION['userId'])) {
            echo "Session not set ";
            return false;
        }
        if($_SESSION['userId'] == 0 ) {
            echo "User Id is 0";
            return false;
        }
        //set the userid if everything is fine.
        $this->userid = $_SESSION['userId'];
        return true;
    }
}
