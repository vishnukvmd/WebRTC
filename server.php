<?php
// Server that helps in setting up a channel between the clients to exchange Signalling Messages

include('init.php');	// Connect to the Database and create tables if not already created
$action = $_GET["action"];


if($action == "sign_in") {	// When a user signs in
	$username = $_GET["username"];
	$result = mysql_query("SELECT * FROM users");	// Check if the username already exists
	$num_rows = mysql_num_rows($result);
	if($num_rows == 0) {
		mysql_query("TRUNCATE TABLE messages");
	}
	$result = mysql_query("SELECT * FROM users WHERE username='".$username."'");	// Check if the username already exists
	$num_rows = mysql_num_rows($result);
	if($num_rows == 1)
		echo "<script>alert('Username already taken!')</script>";
	else {	// If not insert the username into the list of users
		mysql_query("INSERT INTO users (username) VALUES ('".$username."')");
		$result = mysql_query("SELECT * FROM users ORDER BY  `users`.`peer_id` DESC");
		if(!$result) { die(mysql_error()); }
		while($row = mysql_fetch_array($result)) {	// Return the list of currently connected users
			echo $row['username'].':'.$row['peer_id'].',';
		}
	}	
}

if($action == "sign_out") {	// When a user signs out
	$peer_id = $_GET["peer_id"];
	mysql_query("DELETE FROM users WHERE peer_id='".$peer_id."'");	// Delete the user from the 'user' table
	mysql_query("INSERT INTO `test`.`messages` (`from`, `to`, `content`) VALUES ('".$from."', '".$to."', '".$content."');");	// Insert the data into the 'messages' table
}

if($action == "reject") {	// When a user rejects a call
	$remote_id = $_GET["remote_id"];
	$peer_id = $_GET["peer_id"];
	$content = "REJECT";
	mysql_query("INSERT INTO `test`.`messages` (`from`, `to`, `content`) VALUES ('".$peer_id."', '".$remote_id."', '".$content."');");	// Insert the data into the 'messages' table
}


if($action == "message") {	// When a user sends a message
	$from = $_GET["from"];
	$to = $_GET["to"];
	$content = $_POST["content"];
	mysql_query("INSERT INTO `test`.`messages` (`from`, `to`, `content`) VALUES ('".$from."', '".$to."', '".$content."');");	// Insert the data into the 'messages' table
}

if($action == "wait") {	// When a user issues a 'wait'
	$peer_id = $_GET["peer_id"];
	$result = mysql_query("SELECT *  FROM `messages` WHERE `to` LIKE '".$peer_id."'");	// Check if there any messages have been issued so far
	if(!$result) { die(mysql_error()); }
	$num_rows = mysql_num_rows($result);
	if($num_rows < 1) {	// If not
		header('Pragma: '.$peer_id);	// Set the 'Pragma' to the peerId of the user who issued the request
		$result2 = mysql_query("SELECT * FROM users ORDER BY  `users`.`peer_id` DESC");
		if(!$result2) { die(mysql_error()); }
		while($row = mysql_fetch_array($result2)) {	// Send the list of users who have connected after him
			if($row['peer_id'] > $peer_id) {
				echo $row['username'].','.$row['peer_id'].',1';
			}
		}
	}
	else {	// If there have been messages
		while($row = mysql_fetch_array($result)) {	// Send them to the client who requested them
			header('Pragma: '.$row['from']);
			echo $row['content'];			
			mysql_query("DELETE FROM `messages` WHERE `id` LIKE '".$row['id']."'");
		}
	}
	
}
?>