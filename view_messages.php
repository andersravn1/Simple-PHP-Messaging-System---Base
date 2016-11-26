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

?>

<html>

<head>
<title>Simple Messaging System</title>
<link rel="stylesheet" href="default_style.css" type="text/css">
</head>

<body>

<div id="container">
<?php if($loggedIn){ echo'<h3>My messages: ('.$message_count.') <a href="default.php">Back</a></h3>'; }?>
<?php if ($loggedIn){ echo'<p>Currently logged in as user: <i>'.$username.'</i> <a href="#">Logout</a></p>'; } ?>

<ul>

<?php

//get all verified trade data
$privateMessages = array();
$privateMessagesResult = mysqli_query($mysql_connection,"SELECT * FROM messages WHERE message_to='$user_id'");

while ($row = mysqli_fetch_array($privateMessagesResult, MYSQL_ASSOC)) 
{
    $loc['message_id'] = $row["message_id"];
    $loc['message_title'] = $row['message_title'];
    $loc['message_content'] = $row['message_content'];
    $loc['message_from'] = $row['message_from'];
    $loc['message_to'] = $row['message_to'];

    array_push($privateMessages,$loc);
}

if (sizeof($privateMessages) == 0){
	echo '<li>You currently have no new messages.</li>';
}

for ($i = 0;$i < sizeof($privateMessages); $i++) {
	echo '<li>';
	echo '<div class="message">';
	echo '<p><b>Message ID:</b> '.$privateMessages[$i]['message_id'].'</p>';
	echo '<p><b>Sender:</b> '.data_get_username('user_name','users',$privateMessages[$i]['message_from']).'</p>';
	echo '<p><b>Topic:</b> '.$privateMessages[$i]['message_title'].'</p>';
	echo '<p><b>Content:</b></p>';
	echo '<textarea readonly rows="7" cols="54">'.$privateMessages[$i]['message_content'].'</textarea>';
	echo '<a href="create_message.php?receiver='.$privateMessages[$i]["message_from"].'">Respond to message</a>';
	echo '</div>';
	echo '</li>';
}

?>

</ul>

</div>

</body>

</html>