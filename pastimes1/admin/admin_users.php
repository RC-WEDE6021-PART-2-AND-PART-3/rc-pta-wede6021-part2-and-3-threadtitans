<?php

include '../config/DBConn.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'admin') {
    header("Location: admin_login.php");
    exit();
}

if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    if ($id != $_SESSION['user_id']) {
        mysqli_query($conn, "DELETE FROM tblUsers WHERE UserID = $id");
    }
    header("Location: admin_users.php?msg=User deleted");
    exit();
}

if (isset($_GET['verify'])) {
    $id = intval($_GET['verify']);
    mysqli_query($conn, "UPDATE tblUsers SET IsVerified = 'verified' WHERE UserID = $id");
    header("Location: admin_users.php?msg=User verified");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_user'])) {
    $id = intval($_POST['user_id']);
    $user_type = mysqli_real_escape_string($conn, $_POST['user_type']);
    $is_verified = mysqli_real_escape_string($conn, $_POST['is_verified']);
    
    $query = "UPDATE tblUsers SET UserType = '$user_type', IsVerified = '$is_verified' WHERE UserID = $id";
    mysqli_query($conn, $query);
    header("Location: admin_users.php?msg=User updated");
    exit();
}

$users = mysqli_query($conn, "SELECT * FROM tblUsers ORDER BY UserID DESC");
$message = isset($_GET['msg']) ? $_GET['msg'] : '';
?>

<!DOCTYPE html>
<html>
<head>
    <title>User Management - Pastimes</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .admin-container { display: flex; }
        .admin-sidebar { width: 250px; background: #1a1a2e; color: white; padding: 20px; min-height: 100vh; }
        .admin-sidebar a { color: rgba(255,255,255,0.8); text-decoration: none; display: block; padding: 12px 15px; border-radius: 8px; margin: 5px 0; }
        .admin-sidebar a:hover { background: rgba(255,255,255,0.1); }
        .admin-sidebar a.active { background: #e74c3c; }
        .admin-content { flex: 1; padding: 30px; background: #f0f2f5; }
        .alert-success { background: #d4edda; color: #155724; padding: 15px; border-radius: 8px; margin-bottom: 20px; }
        .user-table { background: white; border-radius: 10px; overflow: hidden; width: 100%; border-collapse: collapse; box-shadow: 0 3px 15px rgba(0,0,0,0.08); }
        .user-table th { background: #2c3e50; color: white; padding: 12px 15px; text-align: left; }
        .user-table td { padding: 12px 15px; border-bottom: 1px solid #eee; }
        .btn-sm { padding: 5px 12px; border-radius: 5px; font-size: 0.8rem; text-decoration: none; display: inline-block; margin: 2px; }
        .btn-verify { background: #27ae60; color: white; }
        .btn-delete { background: #e74c3c; color: white; }
        .btn-edit { background: #3498db; color: white; }
    </style>
</head>
<body>
<div class="admin-container">
    <div class="admin-sidebar">
        <h3>👑 Admin Panel</h3>
        <hr>
        <a href="admin_dashboard.php">📊 Dashboard</a>
        <a href="admin_users.php" class="active">👥 Users</a>
        <a href="admin_communication.php">💬 Communications</a>
        <a href="../logout.php">🚪 Logout</a>
    </div>
    
    <div class="admin-content">
        <h1>👥 User Management</h1>
        
        <?php if($message): ?>
            <div class="alert-success">✅ <?php echo $message; ?></div>
        <?php endif; ?>
        
        <table class="user-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Full Name</th>
                    <th>Email</th>
                    <th>Type</th>
                    <th>Verified</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while($user = mysqli_fetch_assoc($users)): ?>
                <tr>
                    <td><?php echo $user['UserID']; ?></td>
                    <td><strong><?php echo htmlspecialchars($user['Username']); ?></strong></td>
                    <td><?php echo htmlspecialchars($user['FullName']); ?></td>
                    <td><?php echo htmlspecialchars($user['Email']); ?></td>
                    <td><?php echo ucfirst($user['UserType']); ?></td>
                    <td><?php echo ucfirst($user['IsVerified']); ?></td>
                    <td>
                        <?php if($user['UserType'] != 'admin'): ?>
                            <?php if($user['IsVerified'] == 'pending'): ?>
                                <a href="?verify=<?php echo $user['UserID']; ?>" class="btn-sm btn-verify">✓ Verify</a>
                            <?php endif; ?>
                            <a href="?delete=<?php echo $user['UserID']; ?>" class="btn-sm btn-delete" 
                               onclick="return confirm('Delete this user?')">🗑 Delete</a>
                        <?php else: ?>
                            <span style="color: #7f8c8d;">(Admin)</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>