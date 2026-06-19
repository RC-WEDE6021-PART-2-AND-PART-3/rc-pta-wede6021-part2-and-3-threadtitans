<?php

include '../config/DBConn.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'admin') {
    header("Location: admin_login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['send_message'])) {
    $user_id = intval($_POST['user_id']);
    $subject = mysqli_real_escape_string($conn, $_POST['subject']);
    $message = mysqli_real_escape_string($conn, $_POST['message']);
    $admin_id = $_SESSION['user_id'];
    
    $query = "INSERT INTO tblCommunications (AdminID, UserID, Subject, Message) 
              VALUES ($admin_id, $user_id, '$subject', '$message')";
    mysqli_query($conn, $query);
    header("Location: admin_communication.php?msg=Message sent");
    exit();
}

$comms = mysqli_query($conn, "SELECT c.*, u.Username, u.FullName 
                              FROM tblCommunications c 
                              JOIN tblUsers u ON c.UserID = u.UserID 
                              ORDER BY c.SentAt DESC");

$users = mysqli_query($conn, "SELECT UserID, Username, FullName, UserType FROM tblUsers WHERE UserType != 'admin'");
$message = isset($_GET['msg']) ? $_GET['msg'] : '';
?>

<!DOCTYPE html>
<html>
<head>
    <title>Communications - Pastimes</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .admin-container { display: flex; }
        .admin-sidebar { width: 250px; background: #1a1a2e; color: white; padding: 20px; min-height: 100vh; }
        .admin-sidebar a { color: rgba(255,255,255,0.8); text-decoration: none; display: block; padding: 12px 15px; border-radius: 8px; margin: 5px 0; }
        .admin-sidebar a:hover { background: rgba(255,255,255,0.1); }
        .admin-sidebar a.active { background: #e74c3c; }
        .admin-content { flex: 1; padding: 30px; background: #f0f2f5; }
        .alert-success { background: #d4edda; color: #155724; padding: 15px; border-radius: 8px; margin-bottom: 20px; }
        .comm-box { background: white; padding: 20px; border-radius: 10px; margin-bottom: 20px; box-shadow: 0 3px 15px rgba(0,0,0,0.08); }
        .comm-message { background: #f8f9fa; padding: 15px; border-radius: 8px; margin: 10px 0; border-left: 4px solid #3498db; }
        .comm-meta { color: #7f8c8d; font-size: 0.85rem; }
    </style>
</head>
<body>
<div class="admin-container">
    <div class="admin-sidebar">
        <h3>👑 Admin Panel</h3>
        <hr>
        <a href="admin_dashboard.php">📊 Dashboard</a>
        <a href="admin_users.php">👥 Users</a>
        <a href="admin_communication.php" class="active">💬 Communications</a>
        <a href="../logout.php">🚪 Logout</a>
    </div>
    
    <div class="admin-content">
        <h1>💬 Communications</h1>
        <p>Send messages to buyers and sellers about deliveries and order status</p>
        
        <?php if($message): ?>
            <div class="alert-success">✅ <?php echo $message; ?></div>
        <?php endif; ?>
        
        <div class="comm-box">
            <h3>📨 Send Message</h3>
            <form method="POST">
                <div class="form-group">
                    <label>Recipient</label>
                    <select name="user_id" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
                        <option value="">Select User</option>
                        <?php while($user = mysqli_fetch_assoc($users)): ?>
                            <option value="<?php echo $user['UserID']; ?>">
                                <?php echo htmlspecialchars($user['FullName']); ?> 
                                (<?php echo ucfirst($user['UserType']); ?>) - 
                                <?php echo htmlspecialchars($user['Username']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Subject</label>
                    <input type="text" name="subject" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
                </div>
                <div class="form-group">
                    <label>Message</label>
                    <textarea name="message" rows="4" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;"></textarea>
                </div>
                <button type="submit" name="send_message" class="btn btn-primary">Send Message</button>
            </form>
        </div>
        
        <h3>📋 Communication History</h3>
        <?php if(mysqli_num_rows($comms) > 0): ?>
            <?php while($comm = mysqli_fetch_assoc($comms)): ?>
                <div class="comm-box">
                    <div class="comm-meta">
                        <strong>To:</strong> <?php echo htmlspecialchars($comm['FullName']); ?> 
                        (@<?php echo htmlspecialchars($comm['Username']); ?>)
                        | <strong>Subject:</strong> <?php echo htmlspecialchars($comm['Subject']); ?>
                        | <strong>Sent:</strong> <?php echo date('M d, Y H:i', strtotime($comm['SentAt'])); ?>
                    </div>
                    <div class="comm-message">
                        <?php echo nl2br(htmlspecialchars($comm['Message'])); ?>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No communications yet.</p>
        <?php endif; ?>
    </div>
</div>
</body>
</html>