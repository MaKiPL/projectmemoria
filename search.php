<!DOCTYPE html>

<html>
<head>
<link rel="stylesheet" type="text/css" href="style.css">
<?php
require_once "conf.php";
session_start();
if(!isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] != TRUE)
{
	header("location: login.php");
	exit;
}
?>
<title>Project Memoria</title>
</head>

	<body>
	<a href="/phpmyadmin">PhpMyAdmin</a>
	<p id="Logo"><a href="index.php">Project Memoria</a></p>
	<p id="Logo2"><a href="insert.php">Add new media</a><a style="margin-left: 1%" href="logout.php"> Logout</a></p>
	<div id="mainWindow">

	
	<?php
	$connection = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
	if($connection->connect_error)
	{
		die("Connection failed at ".$connection->connect_error);
	}
	$tags = trim($_GET['textbox']);
	echo '<p>Wynik wyszukiwań dla: ' . $tags . '</p><br>';
	$delimitedTags = explode(" ", $tags);
	$finalTags = '(';
	$i = 0;
	foreach($delimitedTags as $var)
	{
		if($i < count($delimitedTags)-1)
			$finalTags .= "'" . $var . "',";
		else
			$finalTags .= "'" . $var . "')";
		$i++;
	}
	$quickText = "SELECT * FROM `photos` WHERE `bIsPhoto` = 1 AND `photoId` IN (SELECT DISTINCT `photoId` FROM `tags` WHERE `tag` IN " . $finalTags . " GROUP BY `photoId` HAVING COUNT(*) = ".count($delimitedTags).")";
	//echo($quickText);
	$res = $connection->query($quickText);
	if($res->num_rows > 0)
	{
		echo '<h2>Found ' . $res->num_rows . " photos:</h2>";
		while($resArray = mysqli_fetch_assoc($res))
		{
			echo '<a href="details.php?photoId='.$resArray["photoId"].'"><img id="imgb" width="auto" height="150px" src="photos/' . $resArray["uri"] . '"</img></a>';
		}
	}
	$quickText = "SELECT * FROM `photos` WHERE `bIsPhoto` = 0 AND `photoId` IN (SELECT DISTINCT `photoId` FROM `tags` WHERE `tag` IN " . $finalTags . " GROUP BY `photoId` HAVING COUNT(*) = ".count($delimitedTags).")";
	$res = $connection->query($quickText);
	if($res->num_rows > 0)
	{
		echo '<hr><h2>Found '.$res->num_rows.' video(s):</h2>';
		while($resArray = mysqli_fetch_assoc($res))
		{
			echo '<a href="details.php?photoId='.$resArray["photoId"].'"><video controls id="imgb" width="auto" height="200px" src="photos/' . $resArray["uri"] . '"</video></a>';
		}
	}
	?>
</p>

	</div>
	</body>
</html>