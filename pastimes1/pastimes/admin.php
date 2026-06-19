<?php
include "DBConn.php";

$result = mysqli_query($conn, "SELECT * FROM tblUser WHERE seller_status='pending'");

while($row = mysqli_fetch_assoc($result)){
    echo $row['name']." - Pending ";
    echo "<a href='verify.php?id=".$row['user_id']."'>Verify</a><br>";
}
?>