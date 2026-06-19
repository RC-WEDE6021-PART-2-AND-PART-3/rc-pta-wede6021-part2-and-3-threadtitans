<?php

include 'config/DBConn.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$user_type = $_SESSION['user_type'];

if ($user_type == 'seller') {
    $productQuery = "SELECT COUNT(*) as total FROM tblProducts WHERE SellerID = $user_id";
    $productResult = mysqli_fetch_assoc(mysqli_query($conn, $productQuery));
    
    $soldQuery = "SELECT COUNT(*) as sold FROM tblProducts WHERE SellerID = $user_id AND Status = 'sold'";
    $soldResult = mysqli_fetch_assoc(mysqli_query($conn, $soldQuery));
}

$orderQuery = "SELECT COUNT(*) as orders FROM tblOrders WHERE UserID = $user_id";
$orderResult = mysqli_fetch_assoc(mysqli_query($conn, $orderQuery));

$cartQuery = "SELECT COUNT(*) as cart_items FROM tblCart WHERE UserID = $user_id";
$cartResult = mysqli_fetch_assoc(mysqli_query($conn, $cartQuery));

$messageQuery = "SELECT COUNT(*) as unread FROM tblMessages WHERE ReceiverID = $user_id AND IsRead = 'no'";
$messageResult = mysqli_fetch_assoc(mysqli_query($conn, $messageQuery));

include 'includes/header.php';
?>

<main>
    <div class="dashboard-container">
        <div class="sidebar">
            <h3>Welcome, <?php echo htmlspecialchars($_SESSION['fullname']); ?>!</h3>
            <hr style="margin: 1rem 0;">
            <a href="dashboard.php" class="active">📊 Dashboard Overview</a>
            <a href="dashboard.php?page=profile">👤 My Profile</a>
            <?php if($user_type == 'seller'): ?>
                <a href="add-item.php">➕ Add New Item</a>
                <a href="dashboard.php?page=my_listings">📦 My Listings</a>
            <?php endif; ?>
            <a href="cart.php">🛒 My Cart (<?php echo $cartResult['cart_items']; ?>)</a>
            <a href="dashboard.php?page=messages">💬 Messages 
                <?php if($messageResult['unread'] > 0): ?>
                    <span style="background: red; color: white; padding: 2px 6px; border-radius: 10px;"><?php echo $messageResult['unread']; ?></span>
                <?php endif; ?>
            </a>
            <a href="dashboard.php?page=orders">📋 My Orders</a>
            <a href="logout.php">🚪 Logout</a>
        </div>
        
        <div class="main-content">
            <?php 
            $page = isset($_GET['page']) ? $_GET['page'] : 'overview';
            
            if($page == 'overview'): 
            ?>
                <h2>Dashboard Overview</h2>
                
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-top: 2rem;">
                    <?php if($user_type == 'seller'): ?>
                        <div style="background: #3498db; color: white; padding: 1.5rem; border-radius: 10px; text-align: center;">
                            <h3><?php echo $productResult['total']; ?></h3>
                            <p>Total Listings</p>
                        </div>
                        <div style="background: #27ae60; color: white; padding: 1.5rem; border-radius: 10px; text-align: center;">
                            <h3><?php echo $soldResult['sold']; ?></h3>
                            <p>Items Sold</p>
                        </div>
                    <?php endif; ?>
                    <div style="background: #e74c3c; color: white; padding: 1.5rem; border-radius: 10px; text-align: center;">
                        <h3><?php echo $orderResult['orders']; ?></h3>
                        <p>Orders Placed</p>
                    </div>
                    <div style="background: #f39c12; color: white; padding: 1.5rem; border-radius: 10px; text-align: center;">
                        <h3><?php echo $cartResult['cart_items']; ?></h3>
                        <p>Items in Cart</p>
                    </div>
                </div>
                
            <?php elseif($page == 'my_listings' && $user_type == 'seller'): 
                $listingsQuery = "SELECT * FROM tblProducts WHERE SellerID = $user_id ORDER BY CreatedAt DESC";
                $listings = mysqli_query($conn, $listingsQuery);
            ?>
                <h2>My Listings</h2>
                <a href="add-item.php" class="btn btn-primary" style="margin-bottom: 1rem;">+ Add New Item</a>
                <table class="cart-table">
                    <thead>
                        <tr><th>Image</th><th>Product</th><th>Price</th><th>Status</th><th>Actions</th></tr>
                    </thead>
                    <tbody>
                        <?php while($item = mysqli_fetch_assoc($listings)): ?>
                        <tr>
                            <td><img src="<?php echo $item['ImagePath'] ?: 'https://via.placeholder.com/50'; ?>" width="50"></td>
                            <td><?php echo htmlspecialchars($item['ProductName']); ?></td>
                            <td>R<?php echo number_format($item['Price'], 2); ?></td>
                            <td>
                                <span style="background: <?php echo $item['Status'] == 'approved' ? 'green' : ($item['Status'] == 'pending' ? 'orange' : 'red'); ?>; color: white; padding: 3px 8px; border-radius: 5px;">
                                    <?php echo ucfirst($item['Status']); ?>
                                </span>
                            </td>
                            <td>
                                <a href="edit-item.php?id=<?php echo $item['ProductID']; ?>">Edit</a> | 
                                <a href="delete-item.php?id=<?php echo $item['ProductID']; ?>" onclick="return confirm('Delete this item?')">Delete</a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</main>

<?php include 'includes/footer.php'; ?>