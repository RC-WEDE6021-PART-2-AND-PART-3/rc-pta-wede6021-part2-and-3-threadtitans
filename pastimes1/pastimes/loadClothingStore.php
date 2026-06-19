<?php
include "DBConn.php";

mysqli_query($conn, "DROP TABLE IF EXISTS tblUser");

mysqli_query($conn, "
CREATE TABLE tblClothing (
    clothing_id INT AUTO_INCREMENT PRIMARY KEY,
    item_name VARCHAR(100),
    description VARCHAR(255),
    price DECIMAL(10,2),
    image VARCHAR(255)
)");

echo "Database loaded!";
?>