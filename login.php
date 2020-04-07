<?php
define('PASS', 'gęś');
define('ADMINPASS', 'gęś2');
session_start();
if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true)
{
    header("location: index.php");
    exit;
}

require_once "conf.php";

if($_SERVER["REQUEST_METHOD"] == "POST")
{
    if(empty(trim($_POST["password"])))
    {
        die("No password given");
    }
    if($_POST["password"] == PASS)
        $_SESSION["loggedin"] = TRUE;
    else if($_POST["password"] == ADMINPASS)
        {
            $_SESSION["loggedin"] = TRUE;
            $_SESSION["adminmode"] = TRUE;
        }
    header("location: index.php");
}
?>

<html>
    <head>
<title>Login page</title>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    </head>
    <body>
<h2>Gallery is password protected. Enter password:</h2>
<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="POST">
<label>Password</label>
<input type="text" name="password" class="form-control">
<input type="submit" class="btn btn-primary" value="Login">
</form>
</body>
    </html>