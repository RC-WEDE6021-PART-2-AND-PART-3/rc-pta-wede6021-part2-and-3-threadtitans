<?php

include 'config/DBConn.php';

$error = '';
$success = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $fullname = mysqli_real_escape_string($conn, $_POST['fullname']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $user_type = mysqli_real_escape_string($conn, $_POST['user_type']);
    
    if (empty($username) || empty($fullname) || empty($email) || empty($password)) {
        $error = "All fields are required!";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match!";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters!";
    } else {
      
        $checkQuery = "SELECT * FROM tblUsers WHERE Email = '$email' OR Username = '$username'";
        $checkResult = mysqli_query($conn, $checkQuery);
        
        if (mysqli_num_rows($checkResult) > 0) {
            $error = "Username or email already exists!";
        } else {
            $hashedPassword = md5($password);
            $sellerVerification = ($user_type == 'seller') ? 'pending' : 'pending';
            $isVerified = 'pending';
            
            $insertQuery = "INSERT INTO tblUsers (Username, FullName, Email, PasswordHash, UserType, IsVerified, SellerVerification, Phone) 
                            VALUES ('$username', '$fullname', '$email', '$hashedPassword', '$user_type', '$isVerified', '$sellerVerification', '$phone')";
            
            if (mysqli_query($conn, $insertQuery)) {
                $success = "Registration successful! " . ($user_type == 'seller' ? "Your seller verification is pending admin approval." : "You can now log in.");
            } else {
                $error = "Registration failed: " . mysqli_error($conn);
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Pastimes</title>
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
                <li><a href="login.php">Login</a></li>
                <li><a href="register.php" style="color: #e74c3c;">Register</a></li>
            </ul>
        </div>
    </nav>

    <div class="form-container">
        <h2 style="text-align: center; margin-bottom: 2rem;">Create Account</h2>
        
        <?php if($error): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if($success): ?>
            <div class="alert alert-success"><?php echo $success; ?> <a href="login.php">Login here</a></div>
        <?php endif; ?>
        
        <form method="POST" action="" novalidate>
            <div class="form-group">
                <label>Username *</label>
                <input type="text" name="username" required value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>">
            </div>
            <div class="form-group">
                <label>Full Name *</label>
                <input type="text" name="fullname" required value="<?php echo isset($_POST['fullname']) ? htmlspecialchars($_POST['fullname']) : ''; ?>">
            </div>
            <div class="form-group">
                <label>Email *</label>
                <input type="email" name="email" required value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
            </div>
            <div class="form-group">
                <label>Phone Number</label>
                <input type="tel" name="phone" value="<?php echo isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : ''; ?>">
            </div>
            <div class="form-group">
                <label>I want to: *</label>
                <select name="user_type" required>
                    <option value="buyer">Buy Items</option>
                    <option value="seller">Sell Items (Requires verification)</option>
                </select>
            </div>
            <div class="form-group">
                <label>Password * (min 6 characters)</label>
                <input type="password" name="password" required>
            </div>
            <div class="form-group">
                <label>Confirm Password *</label>
                <input type="password" name="confirm_password" required>
            </div>
            <button type="submit" class="btn-submit">Create Account</button>
        </form>
        <p style="text-align: center; margin-top: 1rem;">Already have an account? <a href="login.php">Login here</a></p>
    </div>

    <?php include 'includes/footer.php'; ?>