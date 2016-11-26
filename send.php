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

function data_get_userid($what,$where,$name){
	global $mysql_connection;
	$sql = "SELECT ".$what." FROM ".$where." WHERE user_name='$name'";
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
	header("location:default.php?loginError=2");
}
}

if (!isset($_POST['topic']) || !isset($_POST['receiver']) || !isset($_POST['content'])){
	header("location:default.php");
}

$receiver = data_get_userid("user_id","users",$_POST['receiver']);
$topic = $_POST['topic'];
$content = $_POST['content'];
$local_userid = data_get('user_id',"users",$_SESSION['session']);

$sql = "INSERT INTO messages(message_title,message_content,message_from,message_to) VALUES ('$topic','$content','$local_userid','$receiver')";
echo $sql;
global $mysql_connection;
if (mysqli_query($mysql_connection,$sql)){
	header("location:default.php?sentMessage=1");
}else{
	header("location:default.php?sentMessage=2");
}

?>

