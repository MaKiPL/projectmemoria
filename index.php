<!DOCTYPE html>

<html>
<head>
	<?php
require_once "conf.php";
session_start();
if(!isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] != TRUE)
{
	header("location: login.php");
	exit;
}
?>
<link rel="stylesheet" type="text/css" href="style.css">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">

<title>Project Memoria v0.1</title>
</head>

	<body>
	<a href="/phpmyadmin">PhpMyAdmin</a>
	<p id="Logo"><a href="index.php">Project Memoria</a></p>
	<p id="Logo2"><a href="insert.php">Add new media</a><a style="margin-left: 1%" href="logout.php"> Logout</a></p>
	<div id="mainWindow">
<p id="welcomeText">Type name, text, tag, anything: </p>
	<form action="search.php">
	<input type="text" id="textbox"  class="form-control" name="textbox">
	<input type="submit" class="btn btn-primary" style="float: right; margin-right:5%" value="Szukaj">
	</form>

	<?php
	$mysql = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
	if($mysql->connect_error)
	{
		die("Connection failed at ".$connection->connect_error);
	}
	$quickText = "SELECT * FROM `randomtext` ORDER BY rand() LIMIT 1";
	$res = $mysql->query($quickText);
	if($res->num_rows > 0)
	{
		$resArray = mysqli_fetch_assoc($res);
		
		echo '<p id="quoteText">"' . $resArray["string"] . '"</p>';
		echo '<p id="quoteTextAuthor">"' . $resArray["author"] . '"</p>';
	}
	
	?>
</p>

	</div>
	</body>
</html>