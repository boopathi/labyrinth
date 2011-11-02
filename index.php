<?php

/* Project	: Labyrinth
 * Author 	: Boopathi Rajaa
 * Concept	: Matrix
 */


$sourceFolder = "";

$needle = strstr($_SERVER["SCRIPT_NAME"],"index.php", true);
$request = substr($_SERVER["REQUEST_URI"],strlen($needle));

//Includes
require_once("./config.inc.php");
require_once("./common.lib.php");

if(empty($request)):
	//Get the user level from database
	echo "home";
	

else:
	//Check if the user has access to the particular level
	//$level = $request
	echo $request;
	
endif;


$FORM = <<<FORM
	<div class="labyrinth_submit">
		<form name="labyrinth_submit" method="POST" action="$needle">
			<input type="text" name="labyrinth_answer" />
			<input type="submit" />
		</form>
	</div>
	<script type="text/javascript">
		function labyrinth_submit_form(target){
			if(typeof labyrinth_submit === "undefined")
				labyrinth_submit.apply(target,event);
		}
	</script>
FORM;

echo $FORM;
?>
<script type="text/javascript">
window.onload=function(){
document.forms.labyrinth_submit.addEventListener("submit", function(evt){
evt.preventDefault();

}, true);
}
</script>
