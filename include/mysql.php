<?php
	$mysql = mysqli_connect($mysql_host, $mysql_username, $mysql_password);
	
	if(!$mysql) {
		header("Status: 503 Service Temporarily Unavailable");
		echo "Couldn't Connect to Database";
		die();
	}
	
	if(!mysqli_select_db($mysql, $mysql_database)) {
		header("Status: 503 Service Temporarily Unavailable");
		echo "Database Not Found";
		die();
	}
?>