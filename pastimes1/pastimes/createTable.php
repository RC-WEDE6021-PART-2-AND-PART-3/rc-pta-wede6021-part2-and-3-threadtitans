<?php
include "DBConn.php";

// Drop table if exists
mysqli_query($conn, "DROP TABLE IF EXISTS tblUser");

// Create table
mysqli_query($conn, "
CREATE TABLE tblUser (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    email VARCHAR(100),
    username VARCHAR(50),
    password VARCHAR(255),
    user_role VARCHAR(20),
    seller_status VARCHAR(20)
)");

// Load data from file
$file = fopen("userData.txt", "r");

while (($data = fgetcsv($file)) !== FALSE) {
    $name = $data[0];
    $email = $data[1];
    $username = $data[2];
    $password = $data[3];
    $role = $data[4];
    $status = $data[5];

    mysqli_query($conn, "
    INSERT INTO tblUser (name,email,username,password,user_role,seller_status)
    VALUES ('$name','$email','$username','$password','$role','$status')");
}

fclose($file);

echo "Table created and data loaded!";
?>