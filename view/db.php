<?php

$servername = "localhost";
$username = "root";
$password = "";
$dbName     = "map";

// Create connection
$con = mysqli_connect($servername, $username, $password, $dbName);

// Check connection
if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
    exit();
  }
    // echo "connect to MySQL: ";
  // $API_KEY = 'AIzaSyBbGf_oYjI5qoewIZi4dp5JXvij6Ml4kVg';

?>