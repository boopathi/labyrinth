<?php

/* Project      : Labyrinth
 * Author       : Boopathi Rajaa
 * Description	: INSTALLATION File
 */

//check if installation is done
$config =
include ("config.inc.php");

if ($config == 1) :
	//then proceed to index.php as installation is done
	header("Location: ./");
else :
	//then continue with the installtion
	if (isset($_POST["config"])) :

	endif;
endif;
