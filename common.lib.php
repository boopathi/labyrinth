<?php

/* Project	: Labyrinth
 * Author 	: Boopathi Rajaa
 * Concept	: Matrix
 * Description:
 */

function connectDB(){
	if(defined(DB_HOST)):
		$db = mysql_connect(DB_HOST, DB_USER, DB_PASS) or die("");
		mysql_select_db(DB_NAME);
		return $db;
	endif;
	return false;
}

