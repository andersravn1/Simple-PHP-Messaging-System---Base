<?php

require("config.php");

if(!isset($_POST['username']) || !isset($_POST['password'])){
	header("location:default.php?loginError=1");
}else{
	authenticate($_POST['username'],$_POST['password']);
}

global $mysql_connection;

function authenticate($username,$password){
	global $mysql_connection;
	$_username = stripslashes($username);
	$_password= hash("sha256",stripslashes($password));
	$sql = "SELECT * FROM users WHERE user_name='$_username' AND user_password='$_password'";
	$count = mysqli_num_rows(mysqli_query($mysql_connection,$sql));

	if ($count == 1){
		$sid = hash("sha256",md5(uniqid(rand(), true)));
		$updt_sql = "UPDATE users SET user_session='$sid' WHERE user_name='$_username' AND user_password='$_password'";
		if (mysqli_query($mysql_connection,$updt_sql) == 1){
			
			session_start();
			$_SESSION['session'] = $sid;
			header("location:default.php?loginSuccess=1");

		}else{
			header("location:default.php?loginError=2");
		}
	}else{
		header("location:default.php?loginError=3");
	}
}

?>