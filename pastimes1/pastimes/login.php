<?php
include "DBConn.php";

if(isset($_POST['login'])){
    $username = $_POST['username'];
    $password = md5($_POST['password']);

    $query = mysqli_query($conn, "SELECT * FROM tblUser WHERE username='$username'");
    
    if(mysqli_num_rows($query) > 0){
        $user = mysqli_fetch_assoc($query);

        if($user['password'] == $password){
            echo "User ".$user['name']." is logged in";
        } else {
            echo "Incorrect password";
        }
    } else {
        echo "User not found. Please register.";
    }
}
?>

<form method="POST">
<input type="text" name="username" required placeholder="Username"><br>
<input type="password" name="password" required placeholder="Password"><br>
<button name="login">Login</button>
</form>