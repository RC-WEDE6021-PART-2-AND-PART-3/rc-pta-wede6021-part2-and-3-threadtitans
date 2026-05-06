<?php

include 'config/DBConn.php';

$product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$query = "SELECT p.*, u.Username as SellerName, u.UserID as SellerID, u.Phone, c.CategoryName 
          FROM tblProducts p
          JOIN tblUsers u ON p.SellerID = u.UserID
          JOIN tblCategories c ON p.CategoryID = c.CategoryID
          WHERE p.ProductID = $product_id AND p.Status = 'approved'";
$result = mysqli_query($conn, $query);

if(mysqli_num_rows($result) == 0) {
    header("Location: browse.php");
    exit();
}

$item = mysqli_fetch_assoc($result);

if(isset($_POST['add_to_cart']) && isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $quantity = intval($_POST['quantity']);
    
    $checkCart = "SELECT * FROM tblCart WHERE UserID = $user_id AND ProductID = $product_id";
    $cartResult = mysqli_query($conn, $checkCart);
    
    if(mysqli_num_rows($cartResult) > 0) {
        $updateCart = "UPDATE tblCart SET Quantity = Quantity + $quantity WHERE UserID = $user_id AND ProductID = $product_id";
        mysqli_query($conn, $updateCart);
    } else {
        $insertCart = "INSERT INTO tblCart (UserID, ProductID, Quantity) VALUES ($user_id, $product_id, $quantity)";
        mysqli_query($conn, $insertCart);
    }
    $cart_success = "Item added to cart!";
}

include 'includes/header.php';
?>

<main style="max-width: 1200px; margin: 2rem auto; padding: 0 2rem;">
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 3rem;">

        <div>
            <img src="<?php echo !empty($item['ImagePath']) ? $item['ImagePath'] : 'https://via.placeholder.com/500x400?text=Pastimes'; ?>" alt="<?php echo htmlspecialchars($item['ProductName']); ?>" style="width: 100%; border-radius: 10px;">
        </div>
        
        <div>
            <h1><?php echo htmlspecialchars($item['ProductName']); ?></h1>
            <p style="color: #7f8c8d;">Brand: <?php echo htmlspecialchars($item['Brand'] ?: 'Not specified'); ?> | Category: <?php echo $item['CategoryName']; ?></p>
            
            <div style="margin: 1rem 0;">
                <span style="font-size: 2rem; color: #e74c3c; font-weight: bold;">R<?php echo number_format($item['Price'], 2); ?></span>
                <?php if($item['OriginalPrice']): ?>
                    <span style="text-decoration: line-through; color: #7f8c8d; margin-left: 1rem;">R<?php echo number_format($item['OriginalPrice'], 2); ?></span>
                <?php endif; ?>
            </div>
            
            <p><strong>Condition:</strong> <?php echo ucfirst($item['Condition']); ?></p>
            <p><strong>Size:</strong> <?php echo htmlspecialchars($item['Size'] ?: 'Not specified'); ?></p>
            
            <div style="margin: 1rem 0;">
                <h3>Description</h3>
                <p><?php echo nl2br(htmlspecialchars($item['Description'])); ?></p>
            </div>
            
            <div style="margin: 1rem 0;">
                <strong>Seller:</strong> <?php echo htmlspecialchars($item['SellerName']); ?>
            </div>
            
            <?php if(isset($_SESSION['user_id']) && $_SESSION['user_id'] != $item['SellerID']): ?>
                <?php if(isset($cart_success)): ?>
                    <div class="alert alert-success"><?php echo $cart_success; ?></div>
                <?php endif; ?>
                
                <form method="POST" style="margin-top: 1rem;">
                    <label>Quantity:</label>
                    <input type="number" name="quantity" value="1" min="1" style="width: 80px; padding: 8px; margin: 10px 0;">
                    <button type="submit" name="add_to_cart" class="btn btn-primary" style="width: 100%;">Add to Cart 🛒</button>
                </form>
                
                <a href="message-seller.php?id=<?php echo $product_id; ?>" class="btn btn-secondary" style="width: 100%; text-align: center; margin-top: 10px;">Contact Seller 💬</a>
            <?php elseif(!isset($_SESSION['user_id'])): ?>
                <p><a href="login.php">Login</a> to purchase or contact the seller.</p>
            <?php elseif($_SESSION['user_id'] == $item['SellerID']): ?>
                <p style="color: #27ae60;">This is your listing. <a href="edit-item.php?id=<?php echo $product_id; ?>">Edit it here</a>.</p>
            <?php endif; ?>
        </div>
    </div>
</main>

<?php include 'includes/footer.php'; ?>