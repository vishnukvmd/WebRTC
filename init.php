<?php
$username = "test";		// Insert Username here
$password = "password";	// Insert Password here
$database = "test";		// Insert the name of the Database here


$sql_connect = mysql_connect("localhost",$username,$password);
if(!$sql_connect) die('Could not connect to server! Error : '.mysql_error());
mysql_select_db($database, $sql_connect);

mysql_query("CREATE TABLE IF NOT EXISTS `messages` (
  `id` int(100) NOT NULL AUTO_INCREMENT,
  `from` varchar(100) NOT NULL,
  `to` varchar(100) NOT NULL,
  `content` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1");

mysql_query("CREATE TABLE IF NOT EXISTS `users` (
  `peer_id` int(100) NOT NULL AUTO_INCREMENT,
  `username` varchar(100) NOT NULL,
  PRIMARY KEY (`peer_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1");

?>