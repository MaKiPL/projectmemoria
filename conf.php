<?php
define('DB_SERVER', 'localhost');
define('DB_USERNAME', "root");
define('DB_PASSWORD', "root");
define('DB_NAME', "memoria");

$mysql = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
if($mysql === false)
    die("DB connect error: ".mysqli_connect_error());
?>