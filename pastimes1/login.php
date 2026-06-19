<?php
include 'config/DBConn.php';

$error = '';
$sticky_email = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];
    
    $sticky_email = $email;
    $hashedPassword = md5($password);
    
    $query = "SELECT * FROM tblUsers WHERE Email = '$email' AND PasswordHash = '$hashedPassword'";
    $result = mysqli_query($conn, $query);
    
    if (mysqli_num_rows($result) == 1) {
        $user = mysqli_fetch_assoc($result);
        
        if ($user['IsVerified'] == 'verified' || $user['UserType'] == 'admin') {
            $_SESSION['user_id'] = $user['UserID'];
            $_SESSION['username'] = $user['Username'];
            $_SESSION['user_type'] = $user['UserType'];
            $_SESSION['fullname'] = $user['FullName'];
            
            if ($user['UserType'] == 'admin') {
                header("Location: admin/admin_dashboard.php");
            } else {
                header("Location: dashboard.php");
            }
            exit();
        } else {
            $error = "Your account is pending verification. Please wait for admin approval.";
        }
    } else {
        $error = "Invalid email or password. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Pastimes</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <nav class="navbar">
        <div class="navbar-container">
            <div class="logo"><a href="index.php">Pastimes<span>.</span></a></div>
            <ul class="nav-links">
                <li><a href="index.php">Home</a></li>
                <li><a href="browse.php">Browse</a></li>
                <li><a href="login.php" style="color: #e74c3c;">Login</a></li>
                <li><a href="register.php">Register</a></li>
            </ul>
        </div>
    </nav>

    <div class="form-container">
        <h2 style="text-align: center; margin-bottom: 2rem;">Login to Pastimes</h2>
        
        <?php if($error): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <form method="POST" action="" novalidate>
            <div class="form-group">
                <label>Email *</label>
                <input type="email" name="email" required value="<?php echo htmlspecialchars($sticky_email); ?>">
            </div>
            <div class="form-group">
                <label>Password *</label>
                <input type="password" name="password" required>
            </div>
            <button type="submit" class="btn-submit">Login</button>
        </form>
        <p style="text-align: center; margin-top: 1rem;">
            <a href="#">Forgot Password?</a> | 
            <a href="register.php">Create Account</a>
        </p>
    </div>

    <?php include 'includes/footer.php'; ?>