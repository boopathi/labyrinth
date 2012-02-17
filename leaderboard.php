<?php

/* Project	: Labyrinth
 * Author 	: Boopathi Rajaa
 * Description:
 */



//Set the Constant LABYRINTH
define("LABYRINTH_CONST", "LABYRINTH APPLICATION");

include("./config.inc.php");
include("./common.lib.php");

connectDB();

function insertIntoLeaderBoard(){
	$truncate = mysql_query("truncate pragyan12_laby.leaderboard");
	if(!$truncate)
		return false;
	$query = mysql_query("insert into pragyan12_laby.leaderboard (rownum, userid, level, attempts, user_email, user_fullname) select t3.rownum, t3.userid, t3.level, t3.attempts, t3.user_email, t3.user_fullname from (select @row:=@row+1 rownum, t2.userid, t2.level, t2.attempts, t2.user_email, t2.user_fullname from (select t1.userid, t1.attempts, t1.user_email, t1.user_fullname, t1.level from (select lu.userid, ua.attempts, cu.user_email, cu.user_fullname, lu.to level FROM pragyan12_laby.user_level lu, ( select att.userid, sum(att.attempts) attempts from pragyan12_laby.user_attempts att group by att.userid) ua, pragyan12_cms.pragyanV3_users cu WHERE lu.userid=cu.user_id AND lu.userid=ua.userid ORDER BY lu.to desc) t1 group by t1.userid order by t1.level desc, t1.attempts asc, t1.userid asc) t2, (select @row:=0) r) t3") or die(mysql_error());
	
	if(!$query) return false;

	//$now = date("F j, Y, g:i a");
	$updateConf = mysql_query("update config set value=NOW() where `key`='leaderboard_updatetime'") or die(mysql_error());
	if($updateConf) return true;
	return false;
}

function lastUpdatedTime() {
	$arr = mysql_fetch_array(mysql_query("select `value` from config where `key`='leaderboard_updatetime'")) or die(mysql_error());
	return $arr[0];
}

function updateLeaderBoard($page){
	$page = escape($page);
	if(preg_match("/^[0-9]+$/", $page) == 0)
		return "Cannot";
	$pagenum = intval($page);
	$from = ($pagenum<1?0:$pagenum-1) * 30;
	$to = 30;
	$query = mysql_query("select * from leaderboard limit $from, $to") or die(mysql_error());
	//$query = mysql_query("select t3.rownum, t3.userid, t3.level, t3.attempts, t3.user_email, t3.user_fullname from (select @row:=@row+1 rownum, t2.userid, t2.level, t2.attempts, t2.user_email, t2.user_fullname from (select t1.userid, t1.attempts, t1.user_email, t1.user_fullname, t1.level from (select lu.userid, ua.attempts, cu.user_email, cu.user_fullname, lu.to level FROM pragyan12_laby.user_level lu, ( select att.userid, sum(att.attempts) attempts from pragyan12_laby.user_attempts att group by att.userid) ua, pragyan12_cms.pragyanV3_users cu WHERE lu.userid=cu.user_id AND lu.userid=ua.userid ORDER BY lu.to desc) t1 group by t1.userid order by t1.level desc, t1.attempts asc, t1.userid asc) t2, (select @row:=0) r) t3 limit $from, $to") or die(mysql_error());
	$ret = array();
	while($qresult = mysql_fetch_assoc($query))
		$ret[] = array(
			"position" => $qresult["rownum"],
			"level"=>intval($qresult["level"])-1,
			"name" =>$qresult["user_fullname"]
		);
	return $ret;
}

$numpagesArr=mysql_fetch_array(mysql_query("select count(*) from leaderboard"));
$numpages = intval($numpagesArr[0])/30 + 1;
$pagenum = isset($_GET['page'])?$_GET['page']:1;

if(isset($_GET['update'])){
	if(isset($_GET['curl'])){
		header("Content-Type: text/plain");
		echo insertIntoLeaderBoard();
		exit(1);
	}
	else
		echo "Update Status: ". insertIntoLeaderBoard();
}

?>
<html>
	<head>
		<title>Labyrinth - LeaderBoard</title>
		<style type="text/css">
		table {
			width: 600px;
			margin: 0 auto;
			border: 1px solid #aaa;
		}
		table td {
			min-width: 100px;
		}
		th { background: #AAA; color: black; }
		tr.evenrow{
			background: #222;
		}
		tr.oddrow {
			background: #111;
		}
		div.pageination{
			background: #030303;
			padding: 3px;
			margin: 20px 0;
		}
		div.pageination a {
			color: #999;
			padding: 3px;
			margin: 0 5px;
			background: #111;
			text-decoration: none;
		}
		div.pageination a.currentpage {
			font-weight: bold;
			color: #ff3333;
			background: #222;
		}
		</style>
	</head>
	<body style="text-align:center;background: black;color: white; font-family: sans-serif;"> 
	<h1>Labyrinth - LeaderBoard</h1>
	<h6>Last Updated : <?php echo lastUpdatedTime();?></h6>
	<div class="pageination">
	<?php
        for($i=1;$i<=$numpages;$i++) {
		if($pagenum == $i) $current="currentpage";
		else $current="";
                echo "<a class='$current' href='./leaderboard.php?page=$i'>$i</a> ";
        }
        ?>
	</div>
	<table style="text-align:center" cellspacing="0" cellpadding="5">
		<tbody>
		<tr>
		<th>Position</th>
		<th>Level</th>
		<th>Name</th>
		</tr>
<?php
	$statsarr = updateLeaderBoard($pagenum);
	for($i=0;$i<count($statsarr);$i++){
		if($i%2)$class="evenrow";
		else $class="oddrow";
		$html = <<<ROW
		<tr class="$class">
			<td>{$statsarr[$i]["position"]}</td>
			<td>{$statsarr[$i]["level"]}</td>
			<td>{$statsarr[$i]["name"]}</td>
		</tr>
ROW;
		echo $html;

	}
?>

	</tbody>
	</table>
	<div class="pageination">
	<?php
	for($i=1;$i<=$numpages;$i++) {
		if($pagenum == $i) $current="currentpage";
		else $current="";
		echo "<a class='$current' href='./leaderboard.php?page=$i'>$i</a> ";
	}
	?>
	</div>
	</body>
</html>
<?php
