<?php
include "DBConn.php";

if(isset($_POST['register'])){
    $name = $_POST['name'];
    $email = $_POST['email'];
    $username = $_POST['username'];
    $password = md5($_POST['password']);

    mysqli_query($conn, "
    INSERT INTO tblUser (name,email,username,password,user_role,seller_status)
    VALUES ('$name','$email','$username','$password','customer','pending')");

    echo "Registered successfully. Wait for admin approval.";
}
?>

<form method="POST">
<input type="text" name="name" required placeholder="Name"><br>
<input type="email" name="email" required><br>
<input type="text" name="username" required><br>
<input type="password" name="password" required><br>
<button name="register">Register</button>
</form>