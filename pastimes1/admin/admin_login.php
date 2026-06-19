<?php

include '../config/DBConn.php';

if (isset($_SESSION['user_id']) && $_SESSION['user_type'] == 'admin') {
    header("Location: admin_dashboard.php");
    exit();
}

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];
    $hashed = md5($password);
    
    $query = "SELECT * FROM tblUsers WHERE Email = '$email' AND PasswordHash = '$hashed' AND UserType = 'admin'";
    $result = mysqli_query($conn, $query);
    
    if (mysqli_num_rows($result) == 1) {
        $admin = mysqli_fetch_assoc($result);
        $_SESSION['user_id'] = $admin['UserID'];
        $_SESSION['username'] = $admin['Username'];
        $_SESSION['user_type'] = 'admin';
        $_SESSION['fullname'] = $admin['FullName'];
        header("Location: admin_dashboard.php");
        exit();
    } else {
        $error = "Invalid admin credentials!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Login - Pastimes</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        body {
            background: linear-gradient(135deg, #1a1a2e, #16213e);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .admin-login {
            background: white;
            padding: 40px;
            border-radius: 15px;
            width: 400px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.3);
        }
        .admin-login h1 {
            text-align: center;
            color: #1a1a2e;
        }
        .admin-login .logo {
            text-align: center;
            font-size: 3rem;
            margin-bottom: 20px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            font-weight: 600;
            margin-bottom: 5px;
        }
        .form-group input {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 8px;
        }
        .btn-admin {
            width: 100%;
            padding: 12px;
            background: #e74c3c;
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
        }
        .btn-admin:hover {
            background: #c0392b;
        }
        .back-link {
            text-align: center;
            margin-top: 15px;
        }
        .back-link a {
            color: #3498db;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="admin-login">
        <div class="logo">👑</div>
        <h1>Admin Login</h1>
        <p style="text-align: center; color: #7f8c8d;">Access the administrative dashboard</p>
        
        <?php if($error): ?>
            <div style="background: #f8d7da; color: #721c24; padding: 10px; border-radius: 5px; margin-bottom: 15px;">
                ❌ <?php echo $error; ?>
            </div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" required value="admin@clothingstore.com">
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" required>
            </div>
            <button type="submit" class="btn-admin">Login as Admin</button>
        </form>
        
        <div class="back-link">
            <a href="../index.php">← Back to Website</a>
        </div>
    </div>
</body>
</html>