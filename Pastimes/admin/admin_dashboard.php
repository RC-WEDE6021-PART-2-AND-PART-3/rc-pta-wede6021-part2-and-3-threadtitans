<?php

include '../config/DBConn.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'admin') {
    header("Location: ../login.php");
    exit();
}

$section = isset($_GET['section']) ? $_GET['section'] : 'dashboard';

if(isset($_GET['verify_user'])) {
    $user_id = intval($_GET['verify_user']);
    mysqli_query($conn, "UPDATE tblUsers SET IsVerified = 'verified' WHERE UserID = $user_id");
    header("Location: admin_dashboard.php?section=users&msg=User verified successfully");
    exit();
}

if(isset($_GET['approve_seller'])) {
    $user_id = intval($_GET['approve_seller']);
    mysqli_query($conn, "UPDATE tblUsers SET SellerVerification = 'approved', IsVerified = 'verified' WHERE UserID = $user_id");
    header("Location: admin_dashboard.php?section=sellers&msg=Seller approved successfully");
    exit();
}

if(isset($_GET['approve_product'])) {
    $product_id = intval($_GET['approve_product']);
    mysqli_query($conn, "UPDATE tblProducts SET Status = 'approved' WHERE ProductID = $product_id");
    header("Location: admin_dashboard.php?section=products&msg=Product approved");
    exit();
}

if(isset($_GET['reject_product'])) {
    $product_id = intval($_GET['reject_product']);
    mysqli_query($conn, "UPDATE tblProducts SET Status = 'rejected' WHERE ProductID = $product_id");
    header("Location: admin_dashboard.php?section=products&msg=Product rejected");
    exit();
}

if(isset($_GET['delete_user'])) {
    $user_id = intval($_GET['delete_user']);
    mysqli_query($conn, "DELETE FROM tblUsers WHERE UserID = $user_id");
    header("Location: admin_dashboard.php?section=users&msg=User deleted");
    exit();
}

if(isset($_GET['delete_product'])) {
    $product_id = intval($_GET['delete_product']);
    mysqli_query($conn, "DELETE FROM tblProducts WHERE ProductID = $product_id");
    header("Location: admin_dashboard.php?section=products&msg=Product deleted");
    exit();
}

$stats = [];

$result = mysqli_query($conn, "SELECT COUNT(*) as count FROM tblUsers WHERE UserType != 'admin'");
$stats['total_users'] = mysqli_fetch_assoc($result)['count'];

$result = mysqli_query($conn, "SELECT COUNT(*) as count FROM tblUsers WHERE IsVerified = 'pending' AND UserType != 'admin'");
$stats['pending_users'] = mysqli_fetch_assoc($result)['count'];

$result = mysqli_query($conn, "SELECT COUNT(*) as count FROM tblUsers WHERE SellerVerification = 'pending' AND UserType = 'seller'");
$stats['pending_sellers'] = mysqli_fetch_assoc($result)['count'];

$result = mysqli_query($conn, "SELECT COUNT(*) as count FROM tblProducts");
$stats['total_products'] = mysqli_fetch_assoc($result)['count'];

$result = mysqli_query($conn, "SELECT COUNT(*) as count FROM tblProducts WHERE Status = 'pending'");
$stats['pending_products'] = mysqli_fetch_assoc($result)['count'];

$result = mysqli_query($conn, "SELECT COUNT(*) as count FROM tblOrders");
$stats['total_orders'] = mysqli_fetch_assoc($result)['count'];

$result = mysqli_query($conn, "SELECT SUM(TotalAmount) as total FROM tblOrders WHERE OrderStatus != 'cancelled'");
$stats['total_revenue'] = mysqli_fetch_assoc($result)['total'] ?? 0;

$message = isset($_GET['msg']) ? $_GET['msg'] : '';

include '../includes/header.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Pastimes</title>
    <style>

        .admin-container {
            display: flex;
            min-height: calc(100vh - 200px);
            background: #f0f2f5;
        }
        
        .admin-sidebar {
            width: 280px;
            background: linear-gradient(135deg, #1a1a2e, #16213e);
            color: white;
            padding: 1.5rem 0;
            position: sticky;
            top: 70px;
            height: calc(100vh - 70px);
            overflow-y: auto;
        }
        
        .admin-sidebar .admin-info {
            padding: 0 1.5rem 1.5rem;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            margin-bottom: 1rem;
        }
        
        .admin-sidebar .admin-name {
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 0.25rem;
        }
        
        .admin-sidebar .admin-email {
            font-size: 0.8rem;
            opacity: 0.7;
        }
        
        .admin-sidebar .nav-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 1.5rem;
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            transition: all 0.3s;
            margin: 4px 0;
        }
        
        .admin-sidebar .nav-item:hover {
            background: rgba(255,255,255,0.1);
            color: white;
        }
        
        .admin-sidebar .nav-item.active {
            background: #e74c3c;
            color: white;
            border-left: 4px solid white;
        }
        
        .admin-sidebar .nav-item .icon {
            width: 24px;
            font-size: 1.2rem;
        }
        
        .admin-sidebar .nav-item .badge {
            background: #e74c3c;
            color: white;
            padding: 2px 8px;
            border-radius: 20px;
            font-size: 0.7rem;
            margin-left: auto;
        }
        
        .admin-main {
            flex: 1;
            padding: 2rem;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .stat-card {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            transition: transform 0.2s;
        }
        
        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .stat-info h3 {
            font-size: 1.8rem;
            font-weight: 700;
            margin: 0;
            color: #2c3e50;
        }
        
        .stat-info p {
            margin: 5px 0 0;
            color: #7f8c8d;
            font-size: 0.85rem;
        }
        
        .stat-icon {
            width: 55px;
            height: 55px;
            background: #f0f2f5;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
        }
        
        .content-card {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }
        
        .card-header {
            padding: 1.2rem 1.5rem;
            border-bottom: 1px solid #e9ecef;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }
        
        .card-header h2 {
            margin: 0;
            font-size: 1.3rem;
            color: #2c3e50;
        }
        
        .search-box {
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 8px;
            width: 250px;
        }
        
        .data-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .data-table th {
            background: #f8f9fa;
            padding: 12px 1.5rem;
            text-align: left;
            font-weight: 600;
            color: #2c3e50;
            border-bottom: 2px solid #e9ecef;
        }
        
        .data-table td {
            padding: 12px 1.5rem;
            border-bottom: 1px solid #e9ecef;
            vertical-align: middle;
        }
        
        .data-table tr:hover {
            background: #f8f9fa;
        }
        
        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        
        .status-pending {
            background: #fff3cd;
            color: #856404;
        }
        
        .status-verified,
        .status-approved {
            background: #d4edda;
            color: #155724;
        }
        
        .status-rejected {
            background: #f8d7da;
            color: #721c24;
        }
        
        .status-sold {
            background: #cce5ff;
            color: #004085;
        }
        
        .action-btns {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }
        
        .btn-sm {
            padding: 5px 12px;
            border-radius: 5px;
            font-size: 0.75rem;
            text-decoration: none;
            display: inline-block;
        }
        
        .btn-success {
            background: #28a745;
            color: white;
        }
        
        .btn-danger {
            background: #dc3545;
            color: white;
        }
        
        .btn-warning {
            background: #ffc107;
            color: #333;
        }
        
        .btn-info {
            background: #17a2b8;
            color: white;
        }
        
        .alert-message {
            background: #d4edda;
            color: #155724;
            padding: 12px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            border-left: 4px solid #28a745;
        }
        
        @media (max-width: 768px) {
            .admin-container {
                flex-direction: column;
            }
            
            .admin-sidebar {
                width: 100%;
                height: auto;
                position: relative;
                top: 0;
            }
            
            .admin-main {
                padding: 1rem;
            }
            
            .stats-grid {
                grid-template-columns: 1fr;
            }
            
            .data-table {
                display: block;
                overflow-x: auto;
            }
        }
    </style>
</head>
<body>

<div class="admin-container">

    <div class="admin-sidebar">
        <div class="admin-info">
            <div class="admin-name">👋 <?php echo htmlspecialchars($_SESSION['fullname']); ?></div>
            <div class="admin-email"><?php echo htmlspecialchars($_SESSION['username']); ?>@pastimes.com</div>
        </div>
        
        <a href="?section=dashboard" class="nav-item <?php echo $section == 'dashboard' ? 'active' : ''; ?>">
            <span class="icon">📊</span>
            <span>Dashboard</span>
        </a>
        
        <a href="?section=users" class="nav-item <?php echo $section == 'users' ? 'active' : ''; ?>">
            <span class="icon">👥</span>
            <span>Users</span>
            <?php if($stats['pending_users'] > 0): ?>
                <span class="badge"><?php echo $stats['pending_users']; ?></span>
            <?php endif; ?>
        </a>
        
        <a href="?section=sellers" class="nav-item <?php echo $section == 'sellers' ? 'active' : ''; ?>">
            <span class="icon">🏪</span>
            <span>Sellers</span>
            <?php if($stats['pending_sellers'] > 0): ?>
                <span class="badge"><?php echo $stats['pending_sellers']; ?></span>
            <?php endif; ?>
        </a>
        
        <a href="?section=products" class="nav-item <?php echo $section == 'products' ? 'active' : ''; ?>">
            <span class="icon">👕</span>
            <span>Products</span>
            <?php if($stats['pending_products'] > 0): ?>
                <span class="badge"><?php echo $stats['pending_products']; ?></span>
            <?php endif; ?>
        </a>
        
        <a href="?section=orders" class="nav-item <?php echo $section == 'orders' ? 'active' : ''; ?>">
            <span class="icon">📦</span>
            <span>Orders</span>
        </a>
        
        <a href="../logout.php" class="nav-item">
            <span class="icon">🚪</span>
            <span>Logout</span>
        </a>
    </div>
    
    <div class="admin-main">
        <?php if($message): ?>
            <div class="alert-message">
                ✅ <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>
        
        <?php if($section == 'dashboard'): ?>
            <h1 style="margin-bottom: 1.5rem;">Admin Dashboard</h1>
            
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-info">
                        <h3><?php echo $stats['total_users']; ?></h3>
                        <p>Total Users</p>
                    </div>
                    <div class="stat-icon">👥</div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-info">
                        <h3><?php echo $stats['pending_users']; ?></h3>
                        <p>Pending Verification</p>
                    </div>
                    <div class="stat-icon">⏳</div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-info">
                        <h3><?php echo $stats['total_products']; ?></h3>
                        <p>Total Products</p>
                    </div>
                    <div class="stat-icon">👕</div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-info">
                        <h3><?php echo $stats['pending_products']; ?></h3>
                        <p>Pending Approval</p>
                    </div>
                    <div class="stat-icon">⏳</div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-info">
                        <h3><?php echo $stats['total_orders']; ?></h3>
                        <p>Total Orders</p>
                    </div>
                    <div class="stat-icon">📦</div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-info">
                        <h3>R<?php echo number_format($stats['total_revenue'], 2); ?></h3>
                        <p>Total Revenue</p>
                    </div>
                    <div class="stat-icon">💰</div>
                </div>
            </div>
            
            <div class="content-card" style="margin-top: 1.5rem;">
                <div class="card-header">
                    <h2>Quick Actions</h2>
                </div>
                <div style="padding: 1.5rem; display: flex; gap: 1rem; flex-wrap: wrap;">
                    <a href="?section=users" class="btn-sm btn-info" style="padding: 10px 20px;">Verify New Users</a>
                    <a href="?section=sellers" class="btn-sm btn-warning" style="padding: 10px 20px;">Approve Sellers</a>
                    <a href="?section=products" class="btn-sm btn-success" style="padding: 10px 20px;">Review Products</a>
                </div>
            </div>
        <?php endif; ?>
        
        <?php if($section == 'users'): 
            $search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
            $userQuery = "SELECT * FROM tblUsers WHERE UserType != 'admin'";
            if($search) {
                $userQuery .= " AND (Username LIKE '%$search%' OR Email LIKE '%$search%' OR FullName LIKE '%$search%')";
            }
            $userQuery .= " ORDER BY IsVerified ASC, CreatedAt DESC";
            $users = mysqli_query($conn, $userQuery);
        ?>
            <div class="content-card">
                <div class="card-header">
                    <h2>👥 User Management</h2>
                    <form method="GET" style="margin: 0;">
                        <input type="hidden" name="section" value="users">
                        <input type="text" name="search" class="search-box" placeholder="Search users..." value="<?php echo htmlspecialchars($search); ?>">
                    </form>
                </div>
                
                <table class="data-table">
                    <thead>
                        <tr><th>ID</th><th>Username</th><th>Full Name</th><th>Email</th><th>Type</th><th>Status</th><th>Actions</th></tr>
                    </thead>
                    <tbody>
                        <?php if(mysqli_num_rows($users) > 0): ?>
                            <?php while($user = mysqli_fetch_assoc($users)): ?>
                            <tr>
                                <td><?php echo $user['UserID']; ?></td>
                                <td><strong><?php echo htmlspecialchars($user['Username']); ?></strong></td>
                                <td><?php echo htmlspecialchars($user['FullName']); ?></td>
                                <td><?php echo htmlspecialchars($user['Email']); ?></td>
                                <td>
                                    <span class="status-badge <?php echo $user['UserType'] == 'seller' ? 'status-approved' : 'status-pending'; ?>">
                                        <?php echo ucfirst($user['UserType']); ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="status-badge <?php echo $user['IsVerified'] == 'verified' ? 'status-approved' : 'status-pending'; ?>">
                                        <?php echo ucfirst($user['IsVerified']); ?>
                                    </span>
                                </td>
                                <td class="action-btns">
                                    <?php if($user['IsVerified'] == 'pending'): ?>
                                        <a href="?verify_user=<?php echo $user['UserID']; ?>&section=users" class="btn-sm btn-success" onclick="return confirm('Verify this user?')">✓ Verify</a>
                                    <?php endif; ?>
                                    <a href="?delete_user=<?php echo $user['UserID']; ?>&section=users" class="btn-sm btn-danger" onclick="return confirm('Delete this user? This action cannot be undone!')">🗑 Delete</a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="7" style="text-align: center;">No users found</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
        
        <?php if($section == 'sellers'): 
            $sellers = mysqli_query($conn, "SELECT * FROM tblUsers WHERE UserType = 'seller' ORDER BY SellerVerification ASC, CreatedAt DESC");
        ?>
            <div class="content-card">
                <div class="card-header">
                    <h2>🏪 Seller Applications</h2>
                </div>
                <table class="data-table">
                    <thead>
                        <tr><th>ID</th><th>Username</th><th>Email</th><th>Phone</th><th>Verification Status</th><th>Actions</th></tr>
                    </thead>
                    <tbody>
                        <?php while($seller = mysqli_fetch_assoc($sellers)): ?>
                        <tr>
                            <td><?php echo $seller['UserID']; ?></td>
                            <td><strong><?php echo htmlspecialchars($seller['Username']); ?></strong></td>
                            <td><?php echo htmlspecialchars($seller['Email']); ?></td>
                            <td><?php echo htmlspecialchars($seller['Phone'] ?: 'Not provided'); ?></td>
                            <td>
                                <span class="status-badge 
                                    <?php echo $seller['SellerVerification'] == 'approved' ? 'status-approved' : ($seller['SellerVerification'] == 'pending' ? 'status-pending' : 'status-rejected'); ?>">
                                    <?php echo ucfirst($seller['SellerVerification']); ?>
                                </span>
                            </td>
                            <td class="action-btns">
                                <?php if($seller['SellerVerification'] == 'pending'): ?>
                                    <a href="?approve_seller=<?php echo $seller['UserID']; ?>&section=sellers" class="btn-sm btn-success" onclick="return confirm('Approve this seller?')">✓ Approve</a>
                                    <a href="?reject_seller=<?php echo $seller['UserID']; ?>&section=sellers" class="btn-sm btn-danger" onclick="return confirm('Reject this seller?')">✗ Reject</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
        
        <?php if($section == 'products'): 
            $status_filter = isset($_GET['filter']) ? $_GET['filter'] : 'pending';
            $productQuery = "SELECT p.*, u.Username as SellerName, c.CategoryName 
                            FROM tblProducts p
                            JOIN tblUsers u ON p.SellerID = u.UserID
                            JOIN tblCategories c ON p.CategoryID = c.CategoryID";
            if($status_filter != 'all') {
                $productQuery .= " WHERE p.Status = '$status_filter'";
            }
            $productQuery .= " ORDER BY FIELD(p.Status, 'pending', 'approved', 'rejected'), p.CreatedAt DESC";
            $products = mysqli_query($conn, $productQuery);
        ?>
            <div class="content-card">
                <div class="card-header">
                    <h2>📦 Product Management</h2>
                    <div style="display: flex; gap: 10px;">
                        <a href="?section=products&filter=pending" class="btn-sm <?php echo $status_filter == 'pending' ? 'btn-warning' : 'btn-info'; ?>">Pending</a>
                        <a href="?section=products&filter=approved" class="btn-sm <?php echo $status_filter == 'approved' ? 'btn-success' : 'btn-info'; ?>">Approved</a>
                        <a href="?section=products&filter=rejected" class="btn-sm <?php echo $status_filter == 'rejected' ? 'btn-danger' : 'btn-info'; ?>">Rejected</a>
                        <a href="?section=products&filter=all" class="btn-sm btn-info">All</a>
                    </div>
                </div>
                <table class="data-table">
                    <thead>
                        <tr><th>ID</th><th>Image</th><th>Product</th><th>Seller</th><th>Category</th><th>Price</th><th>Status</th><th>Actions</th></tr>
                    </thead>
                    <tbody>
                        <?php while($product = mysqli_fetch_assoc($products)): ?>
                        <tr>
                            <td><?php echo $product['ProductID']; ?></td>
                            <td>
                                <img src="<?php echo $product['ImagePath'] ?: 'https://via.placeholder.com/40'; ?>" width="40" height="40" style="object-fit: cover; border-radius: 5px;">
                            </td>
                            <td><strong><?php echo htmlspecialchars($product['ProductName']); ?></strong></td>
                            <td><?php echo htmlspecialchars($product['SellerName']); ?></td>
                            <td><?php echo $product['CategoryName']; ?></td>
                            <td>R<?php echo number_format($product['Price'], 2); ?></td>
                            <td>
                                <span class="status-badge 
                                    <?php echo $product['Status'] == 'approved' ? 'status-approved' : ($product['Status'] == 'pending' ? 'status-pending' : 'status-rejected'); ?>">
                                    <?php echo ucfirst($product['Status']); ?>
                                </span>
                            </td>
                            <td class="action-btns">
                                <?php if($product['Status'] == 'pending'): ?>
                                    <a href="?approve_product=<?php echo $product['ProductID']; ?>&section=products&filter=<?php echo $status_filter; ?>" class="btn-sm btn-success" onclick="return confirm('Approve this product?')">✓ Approve</a>
                                    <a href="?reject_product=<?php echo $product['ProductID']; ?>&section=products&filter=<?php echo $status_filter; ?>" class="btn-sm btn-danger" onclick="return confirm('Reject this product?')">✗ Reject</a>
                                <?php endif; ?>
                                <a href="?delete_product=<?php echo $product['ProductID']; ?>&section=products&filter=<?php echo $status_filter; ?>" class="btn-sm btn-danger" onclick="return confirm('Delete this product?')">🗑 Delete</a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
        
        <?php if($section == 'orders'): 
            $orders = mysqli_query($conn, "SELECT o.*, u.Username FROM tblOrders o JOIN tblUsers u ON o.UserID = u.UserID ORDER BY o.OrderDate DESC");
        ?>
            <div class="content-card">
                <div class="card-header">
                    <h2>📋 Order Management</h2>
                </div>
                <table class="data-table">
                    <thead>
                        <tr><th>Order ID</th><th>Customer</th><th>Date</th><th>Total</th><th>Payment</th><th>Status</th><th>Actions</th></tr>
                    </thead>
                    <tbody>
                        <?php while($order = mysqli_fetch_assoc($orders)): ?>
                        <tr>
                            <td>#<?php echo $order['OrderID']; ?></td>
                            <td><?php echo htmlspecialchars($order['Username']); ?></td>
                            <td><?php echo date('M d, Y', strtotime($order['OrderDate'])); ?></td>
                            <td>R<?php echo number_format($order['TotalAmount'], 2); ?></td>
                            <td><?php echo str_replace('_', ' ', ucfirst($order['PaymentMethod'])); ?></td>
                            <td>
                                <span class="status-badge status-pending"><?php echo ucfirst($order['OrderStatus']); ?></span>
                            </td>
                            <td>
                                <a href="order-details.php?id=<?php echo $order['OrderID']; ?>" class="btn-sm btn-info">View</a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include '../includes/footer.php'; ?>