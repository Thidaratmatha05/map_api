<?php

$servername = "localhost";
$username = "root";
$password = "";
$dbName = "map";

// Create connection
$con = mysqli_connect($servername, $username, $password, $dbName);
$conn = mysqli_connect($servername, $username, $password, $dbName);

// Check connection
if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
    exit();
  }
// echo "connect to MySQL: ";
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
// echo "Connected successfully";

?>