<?php

/* Project      : Labyrinth
 * Author       : Boopathi Rajaa
 * Description	: INSTALLATION File
 */

$TEMPLATE_BODY="";

//check if installation is done
if(file_exists("./config.inc.php")) :
	$config = include ("./config.inc.php");
	if ($config === "LABYRINTH") :
		//then proceed to index.php as installation is done
		header("Location: ./");
	else :
		//then continue with the installtion
		if (isset($_POST["config"])) :
			$conf = <<<CONFF
<?php
/* Project	: Labyrinth
 * Author 	: Boopathi Rajaa
 * Concept	: Matrix
 */

define("DB_HOST","{$_POST['dbhost']}");
define("DB_USER","{$_POST['dbuser']}");
define("DB_PASS","{$_POST['dbpass']}");
define("DB_NAME","{$_POST['dbname']}");

//IMPORTANT - If 0, then config is not done
return "LABYRINTH";
	
CONFF;
			$writable_flag = true;
			if(!is_writable("./config.inc.php")){
				$writable_flag=false;
				$TEMPLATE_BODY = <<<NOTWRITABLE
				The config file config.inc.php is not writable by http-user
				<address>sudo chown http-user config.inc.php</address>
NOTWRITABLE;
			}
			if(!is_dir("./images")){
				if(!mkdir("./images",0755)){
					$writable_flag=false;
					$TEMPLATE_BODY=<<<NOTWRITABLE
					Folder <em>images</em> could not be created.
NOTWRITABLE;
				}
			} else {
				if(!is_dir("./images/questions")){
					if(!mkdir("./images/questions",0755)){
						$writable_flag = false;
						$TEMPLATE_BODY=<<<NOTWRITABLE
							Folder <em>images/questions</em> could not be created.
NOTWRITABLE;	
					}
				}
			}
			if($writable_flag === true) {
				$conf_file = fopen("./config.inc.php", "w");
				fwrite($conf_file, $conf);
				fclose($conf_file);
				header("Location: ./");
			} else {}
		else:
			//then show a form containing the fields required for config
			$TEMPLATE_BODY = <<<FORM
				<form name="laby_install" action="./install.php" method="POST">
					<input type="hidden" name="config" value="1" />
					<table>
						<tbody>
							<tr>
								<td>Database Name</td>
								<td><input type="text" name="dbhost" value="localhost" /></td>
							</tr>
							<tr>
								<td>Database Name</td>
								<td><input type="text" name="dbname" placeholder="labyrinth" /></td>
							</tr>
							<tr>
								<td>Username to connect to database</td>
								<td><input type="text" name="dbuser" value="" placeholder="root"/></td>
							</tr>
							<tr>
								<td>Password</td>
								<td><input type="password" name="dbpass" placeholder="P@55W0RD" /></td>
							</tr>
							<tr>
								<td colspan="2" align="center"><input type="submit" value="Make Config"/></td>
							</tr>
						</tbody>
					</table>
				</form>	
FORM;
		endif;
	endif;
else:
	//then create a file and then redirect to the same page for config
	if(is_writable("./config.inc.php")){
		$conf = fopen("./config.inc.php","w");
		fwrite($conf, "<?php //created config");
		fclose($conf);
		header("Location: ./install.php");
	}
	else {
		$TEMPLATE_BODY = <<<FAIL
		The file config.inc.php does not exist and the http-user could not create it for you. \
		<ol>
			<li>Create a File named <em>config.inc.php</em></li>
			<li>Give write permissions to http-user</li>
			<li><a href="./install.php">Reload</a> this page</li>
		</ol>
FAIL;
	}
endif;
//ECHO the template
?>
<html>
	<head>
		<title>Labyrinth Installation</title>
		<style type="text/css">
			body {
				font-family: sans-serif;
				font-size: 13px;
				background: #FFF;
			}
			.content{
				width: 450px;
				margin: 50px auto;
				background: #f7f7f7;
				-moz-border-radius: 10px;
				-webkit-border-radius: 10px;
				border-radius: 10px;
				border: solid 1px #DDD;
				padding: 15px;
				font-family: sans-serif;
				font-size: 0.7em;
			}
			h1 {
				text-align: center;
			}
			table {
				margin: auto;
			}
			td{
				font-size: 0.7em; 
			}
		</style>
	</head>
	<body>
		<div class="content">
			<h1>Labyrinth - Install</h1>
			<?php echo $TEMPLATE_BODY; ?>
		</div>
	</body>
</html>
<?php
