<?php

require("config.php");

function data_get($what,$where,$session){
	global $mysql_connection;
	$sql = "SELECT ".$what." FROM ".$where." WHERE user_session='$session'";
	return mysqli_fetch_array(mysqli_query($mysql_connection,$sql),MYSQL_NUM)[0];
}

function data_get_username($what,$where,$id){
	global $mysql_connection;
	$sql = "SELECT ".$what." FROM ".$where." WHERE user_id='$id'";
	if (mysqli_num_rows(mysqli_query($mysql_connection,$sql)) == 1){
	return mysqli_fetch_array(mysqli_query($mysql_connection,$sql),MYSQL_NUM)[0];
}else{
	return "Unknown";
}
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
$receiver = null;

if (isset($_SESSION['session'])){
	if (data_get("*","users",$_SESSION['session'])){

	$loggedIn = true;
	$username = data_get("user_name","users",$_SESSION['session']);
	$user_id = data_get("user_id","users",$_SESSION['session']);
	$message_count = data_get_message_count("message_id","messages",$user_id);
}else{
	unset($_SESSION['session']);
	session_destroy();
	header("location:default.php?loginError=2");
}
}

if (isset($_GET['receiver'])){
	$receiver = data_get_username("user_name","users",$_GET['receiver']);
}else{ 
	$receiver = "";
}

?>


<html>

<head>
<title>Simple Messaging System</title>
<link rel="stylesheet" href="default_style.css" type="text/css">
</head>

<body>

<div id="container">
<?php if($loggedIn){ echo'<h3>Create a new message - <a href="default.php">Back</a></h3>'; }?>
<?php if ($loggedIn){ echo'<p>Currently logged in as user: <i>'.$username.'</i> <a href="#">Logout</a></p>'; } ?>

<div id="msgBox">

<form action="send.php" method="post">
<p>Receiver: <input type="text" name="receiver" value="<?php echo $receiver; ?>"></p>
<p>Topic: <input type="text" name="topic"></p>
<p>Content: </p>
<textarea rows="7" cols="61" name="content">Enter message content</textarea><br><br>
<input type="submit" value="Send">

</form>

</div>

</div>

</body>

</html>