<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pastimes - Buy & Sell Second-Hand Branded Clothing</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <nav class="navbar">
        <div class="navbar-container">
            <div class="logo">
                <a href="index.php">Pastimes<span>.</span></a>
            </div>
            <ul class="nav-links">
                <li><a href="index.php">🏠 Home</a></li>
                <li><a href="browse.php">🔍 Browse</a></li>
                
                <?php if(isset($_SESSION['user_id'])): ?>
                    
                    <li><a href="dashboard.php">📊 Dashboard</a></li>
                    
                    <?php if($_SESSION['user_type'] == 'seller' || $_SESSION['user_type'] == 'admin'): ?>
                        <li><a href="add-item.php">➕ Sell Now</a></li>
                    <?php endif; ?>
                    
                    <li><a href="cart.php">🛒 Cart</a></li>
                    
                    <?php if($_SESSION['user_type'] == 'admin'): ?>
                        <li><a href="admin/admin_dashboard.php" style="background: #e74c3c; padding: 8px 15px; border-radius: 20px;">👑 Admin Panel</a></li>
                    <?php endif; ?>
                    
                    <li><a href="logout.php">🚪 Logout (<?php echo htmlspecialchars($_SESSION['username']); ?>)</a></li>
                    
                <?php else: ?>
                    <li><a href="login.php">🔐 Login</a></li>
                    <li><a href="register.php">📝 Register</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>