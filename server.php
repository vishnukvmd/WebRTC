<?php
include('init.php');
$action = $_GET["action"];


if($action == "sign_in") {
	$username = $_GET["username"];
	$result = mysql_query("SELECT * FROM users WHERE username='".$username."'");
	$num_rows = mysql_num_rows($result);
	if($num_rows == 1)
		echo "<script>alert('Username already taken!')</script>";
	else {
		mysql_query("INSERT INTO users (username) VALUES ('".$username."')");
		$result = mysql_query("SELECT * FROM users ORDER BY  `users`.`peer_id` DESC");
		if(!$result) { die(mysql_error()); }
		while($row = mysql_fetch_array($result)) {
			echo $row['username'].':'.$row['peer_id'].',';
		}
	}	
}

if($action == "sign_out") {
	$peer_id = $_GET["peer_id"];
	mysql_query("DELETE FROM users WHERE peer_id='".$peer_id."'");
}

if($action == "message") {
	$from = $_GET["from"];
	$to = $_GET["to"];
	$content = $_POST["content"];
	mysql_query("INSERT INTO `test`.`messages` (`from`, `to`, `content`) VALUES ('".$from."', '".$to."', '".$content."');");
}

if($action == "wait") {
	$peer_id = $_GET["peer_id"];
	$result = mysql_query("SELECT *  FROM `messages`");
	if(!$result) { die(mysql_error()); }
	$num_rows = mysql_num_rows($result);
	if($num_rows < 1) {
		header('Pragma: '.$peer_id);
		$result = mysql_query("SELECT * FROM users ORDER BY  `users`.`peer_id` DESC");
		if(!$result) { die(mysql_error()); }
		while($row = mysql_fetch_array($result)) {
			if($row['peer_id'] > $peer_id) {
				echo $row['username'].','.$row['peer_id'].',1';
			}
		}
	}
	else {
		$result = mysql_query("SELECT *  FROM `messages` WHERE `to` LIKE '".$peer_id."'");
		if(!$result) { die(mysql_error()); }
		while($row = mysql_fetch_array($result)) {
			header('Pragma: '.$row['from']);
			echo $row['content'];
			mysql_query("DELETE FROM messages WHERE `id` LIKE '".$row['id']."'");
		}
	}
	
}
?>