<?php
require_once "conf.php";
$displayMessage = '';
$title = '';
error_reporting(E_ALL | E_STRICT);
if($_SERVER["REQUEST_METHOD"] == "POST")
{
    if(isset($_POST["title"]) && isset($_POST["tags"]))
    {
    $target = "photos/";
    $target .= basename($_FILES["fileupload"]["name"]);
    $fileType = strtolower(pathinfo($target,PATHINFO_EXTENSION));
    $check = 1;
    }
    else
    {
        $displayMessage = "error - mandatory fields not filled";
        $check = 0;
    }
    if(empty(trim($_POST["tags"])))
    {
        $displayMessage = "error - empty tag";
        $check = 0;
    }
    if(empty(trim($_FILES["fileupload"]["name"])))
    {
        $displayMessage = "error - empty file";
        $check = 0;
    }
    $displayMessage .= "added: " . $title;
    if($check == 1)
    {
        
        if(!isset($_FILES["fileupload"]["error"]))
            die("unknown upload error");
        if($_FILES["fileupload"]["size"] > 10000000)
            die("Too large file. Max is 10MB");
        $target = sprintf("photos/%s.%s", 
        sha1_file($_FILES["fileupload"]["tmp_name"]),
        $fileType);
        if(file_exists($target))
            {
                $displayMessage = "error: file exists";
            }
        else //Success
            {                   
                $bIsPhoto = 1;
                $uploadMime = mime_content_type($_FILES["fileupload"]["tmp_name"]);
                if(substr($uploadMime, 0, 5) === "video")
                    $bIsPhoto = 0;

                $sqlInsert = "INSERT INTO `photos` (`photoId`, `title`, `bIsPhoto`, `uri`) VALUES (NULL, ?,?,?)";
                $statement = $mysql->prepare($sqlInsert);
                $fileName = substr($target, 7);
                $statement->bind_param("sis", $_POST["title"], $bIsPhoto, $fileName);
                if(!$statement->execute())
                {
                    die("error statement execute");
                }
                
                $sqlGetMax = "SELECT MAX(`photoId`) FROM `photos`";
                $maxPhoto = $mysql->query($sqlGetMax);
                if($maxPhoto->num_rows != 1)
                {
                    die("error at get max photoId");
                    exit;
                }
                $maxId = $maxPhoto->fetch_array()[0]; //get maxId
                
                $tagsTrimmed = trim($_POST["tags"]);
                $tagsArray = explode(" ", $tagsTrimmed);
                foreach($tagsArray as $tag)
                {
                    $sqlInsert = "INSERT INTO `tags` (`photoId`, `tag`) VALUES (?, ?)";
                    $statement = $mysql->prepare($sqlInsert);
                    $statement->bind_param("is", $maxId, $tag);
                    if(!$statement->execute())
                    {
                        die("error statement tags execute");
                    }
                }
                move_uploaded_file($_FILES["fileupload"]["tmp_name"], $target);
            }
    }
    else
    {
        $displayMessage = "error";
    }
}
?>

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
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="style.css">
</head>


<body>
<a href="/phpmyadmin">PhpMyAdmin</a>
<p id="Logo"><a href="index.php">Project Memoria</a></p>
	<p id="Logo2"><a href="insert.php">Add new media</a><a style="margin-left: 1%" href="logout.php"> Logout</a></p>
    <div id="mainWindow">
<p></p>
<p>
    <?php echo $displayMessage?>
</p>
<h2>Add new media</h2>

<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="POST" enctype="multipart/form-data">
<input type="file" name="fileupload" id="fileupload"></br>
<label>Title (optional)</label>
<input type="text" name="title" id="title" class="form-control">
<label>Tags (space separated)</label>
<input type="text" name="tags" id="tags" class="form-control">
<!--
<select id="bIsPhoto" name="bIsPhoto" class="custom-select d-block w-100">
    <option value="1">Photo</option>
    <option value="0">Video</option>
    -->
    <input type="submit" class="btn btn-primary" value="Add">
</form>
</div>
</body>
    </html>