<?php
include "DBConn.php";

$id = $_GET['id'];

mysqli_query($conn, "UPDATE tblUser SET seller_status='verified' WHERE user_id=$id");

echo "User verified!";
?>