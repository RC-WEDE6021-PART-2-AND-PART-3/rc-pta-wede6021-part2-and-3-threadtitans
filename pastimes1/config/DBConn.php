<?php

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "pastimes_new";

$conn = mysqli_connect("localhost", "root", "", "pastimes");

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

mysqli_set_charset($conn, "utf8");

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

date_default_timezone_set('Africa/Johannesburg');
?>