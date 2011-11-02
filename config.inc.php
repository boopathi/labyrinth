<?php

/* Project	: Labyrinth
 * Author 	: Boopathi Rajaa
 * Concept	: Matrix
 * Description:
 * Configuration File - stores database login details and admin authentication
 */

if(!defined('LABYRINTH_CONST')):
	echo <<<ERROR
	<h1>Access Denied</h1>
	This vulnerable activity will be reported to the Administrator.
ERROR;
	exit(1);
endif;

define("DB_HOST","localhost");
define("DB_USER","root");
define("DB_PASS","root");
define("DB_NAME","labyrinth");
