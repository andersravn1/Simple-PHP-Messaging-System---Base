<?php

require("config.php");

function data_get($what,$where,$session){
	global $mysql_connection;
	$sql = "SELECT ".$what." FROM ".$where." WHERE user_session='$session'";
	return mysqli_fetch_array(mysqli_query($mysql_connection,$sql),MYSQL_NUM)[0];
}

function data_get_message_count($what,$where,$id){
	global $mysql_connection;
	$sql = "SELECT ".$what." FROM ".$where." WHERE message_to='$id'";
	return mysqli_num_rows(mysqli_query($mysql_connection,$sql));
}

session_start();

$loggedIn = null;
$username = null;
$user_id = null;
$message_count = null;

if (isset($_SESSION['session'])){
	if (data_get("*","users",$_SESSION['session'])){

	$loggedIn = true;
	$username = data_get("user_name","users",$_SESSION['session']);
	$user_id = data_get("user_id","users",$_SESSION['session']);
	$message_count = data_get_message_count("message_id","messages",$user_id);
}else{
	unset($_SESSION['session']);
	session_destroy();
}
}

?>

<html>

<head>
<title>Simple Messaging System</title>
<link rel="stylesheet" href="default_style.css" type="text/css">
</head>

<body>

<div id="container">
<h3>What do you wish to do?</h3>
<?php if ($loggedIn){ echo'<p>Currently logged in as user: <i>'.$username.'</i> <a href="#">Logout</a></p>'; } ?>
<ul>
<?php if (!$loggedIn){ echo'<li><a href="default_login.php">Login</a></li>'; } ?>
<?php if ($loggedIn){ echo'<li><a href="create_message.php">Send Message</a></li>'; } ?>
<?php if ($loggedIn){ echo'<li><a href="view_messages.php">View Messages ('.$message_count.')</a></li>'; } ?>

</ul>
</div>

</body>

</html>