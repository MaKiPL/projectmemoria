<!DOCTYPE html>

<html>
<head>
<link rel="stylesheet" type="text/css" href="style.css">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
<?php
require_once "conf.php";
session_start();
if(!isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] != TRUE)
{
	header("location: login.php");
	exit;
}
$photoId = $_GET["photoId"];

if($_SERVER["REQUEST_METHOD"] == "POST")
{
if($_POST["Update"] == "Update")
{
	if(isset($_POST["tags"]))
	{
	$mysql = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
	if($mysql->connect_error)
	{
		die("Connection failed at ".$connection->connect_error);
	}
	$deleteQuery = $mysql->prepare("DELETE FROM `tags` WHERE `photoId` = ?");
	$deleteQuery->bind_param("i", $photoId);
	$deleteQuery->execute();
	$insertQuery = $mysql->prepare("INSERT INTO `tags` (`photoId`, `tag`) VALUES (?,?)");
	$tags = explode(" ", trim($_POST["tags"]));
	foreach($tags as $tag)
	{
		$insertQuery->bind_param("is", $photoId, $tag);
		$insertQuery->execute();
	}
	}
}
else if($_POST["Update"] == "Remove")
{
	$mysql = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME); #explicit for intellisense
	if($mysql->connect_error)
	{
		die("Connection failed at ".$connection->connect_error);
	}
	$deleteQuery = $mysql->prepare("DELETE FROM `tags` WHERE `photoId` = ?");
	$deleteQuery->bind_param("i", $photoId);
	$deleteQuery->execute();
	$quickText = "SELECT * FROM `photos` where `photoId` = $photoId";
	$res = $mysql->query($quickText);
	$fetch = $res->fetch_assoc();
	$filePath = "photos/".$fetch["uri"];
	unlink($filePath);
	$deleteQuery = $mysql->prepare("DELETE FROM `photos` WHERE `photoId` = ?");
	$deleteQuery->bind_param("i", $photoId);
	$deleteQuery->execute();
}
else {
	die("Unknown POST");
}
}
?>
<title>Project Memoria</title>
</head>

	<body>
	<a href="/phpmyadmin">PhpMyAdmin</a>
	<p id="Logo"><a href="index.php">Project Memoria</a></p>
	<p id="Logo2"><a href="insert.php">Add new photo</a><a style="margin-left: 1%" href="logout.php"> Logout</a></p>
	<div id="mainWindow">



	<?php
	$imageSection = "";
	$tagSection = "";
	if(isset($_GET['photoId']))
	{
		$mysql = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
		if($mysql->connect_error)
		{
			die("Connection failed at ".$connection->connect_error);
		}
	$photoId = trim($_GET['photoId']);
	$quickText = "SELECT * FROM `photos` WHERE `photoId` = $photoId";
	echo("<br>");
	$res = $mysql->query($quickText);
	if($res->num_rows > 0)
	{
		$resArray = mysqli_fetch_assoc($res);
		$imageSection = '<a href="photos/'.$resArray["uri"].'"><img id="imgb" align="top" height="350px" src="photos/' . $resArray["uri"] . '"</img></a>';
		
		$statementSql = "SELECT `tag` FROM `tags` WHERE `photoId` = ".$photoId;
		$res = $mysql->query($statementSql);
		while($tagArray = $res->fetch_array())
		{
			$tagSection .= $tagArray[0]. " ";
		}
		$tagSection = trim($tagSection);
		//if(isset($_SESSION["adminmode"]))
		//{
		//	$tagSection .= "<br>ADMIN MODE";
		//}
	}
	else {
		$imageSection = "NO PHOTO FOUND";
	}
}
	?>
	<div id="leftColumn">
	<?php echo($imageSection);?>
</div>
	<div id="rightColumn">
	<?php 
	if(isset($_SESSION["adminmode"]))
	{
	echo "<form action=".htmlspecialchars($_SERVER["PHP_SELF"])."?photoId=".$photoId.' method="POST">';
echo('<label>Tags</label>');
echo('<input type="text" name="tags" class="form-control" value="'.$tagSection.'">');
echo('<input type="submit" name="Update" class="btn btn-primary" value="Update">');
echo('<input type="submit" name="Update" class="btn btn-danger" value="Remove">');
echo('</form>');
	}
	else {
		echo('<input type="text" name="tags" class="form-control" value="'.$tagSection.'">');
	}
?>
</div>

	</div>
	</body>
</html>