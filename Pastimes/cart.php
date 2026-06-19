<?php

include 'config/DBConn.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if(isset($_GET['remove'])) {
    $cart_id = intval($_GET['remove']);
    mysqli_query($conn, "DELETE FROM tblCart WHERE CartID = $cart_id AND UserID = $user_id");
    header("Location: cart.php");
    exit();
}

if(isset($_POST['update_cart'])) {
    foreach($_POST['quantity'] as $cart_id => $qty) {
        $qty = max(1, intval($qty));
        mysqli_query($conn, "UPDATE tblCart SET Quantity = $qty WHERE CartID = $cart_id AND UserID = $user_id");
    }
    header("Location: cart.php");
    exit();
}

$query = "SELECT c.*, p.ProductName, p.Price, p.ImagePath, p.ProductID, p.SellerID, u.Username as SellerName 
          FROM tblCart c
          JOIN tblProducts p ON c.ProductID = p.ProductID
          JOIN tblUsers u ON p.SellerID = u.UserID
          WHERE c.UserID = $user_id";
$result = mysqli_query($conn, $query);

$total = 0;

include 'includes/header.php';
?>

<main style="max-width: 1200px; margin: 2rem auto; padding: 0 2rem;">
    <h1 class="section-title">My Cart</h1>
    
    <?php if(mysqli_num_rows($result) > 0): ?>
        <form method="POST">
            <table class="cart-table">
                <thead>
                    <tr><th>Product</th><th>Price</th><th>Quantity</th><th>Subtotal</th><th></th></tr>
                </thead>
                <tbody>
                    <?php while($item = mysqli_fetch_assoc($result)): 
                        $subtotal = $item['Price'] * $item['Quantity'];
                        $total += $subtotal;
                    ?>
                    <tr>
                        <td>
                            <img src="<?php echo $item['ImagePath'] ?: 'https://via.placeholder.com/50'; ?>" width="50" style="vertical-align: middle;">
                            <?php echo htmlspecialchars($item['ProductName']); ?>
                            <br><small>Seller: <?php echo htmlspecialchars($item['SellerName']); ?></small>
                        </td>
                        <td>R<?php echo number_format($item['Price'], 2); ?></td>
                        <td>
                            <input type="number" name="quantity[<?php echo $item['CartID']; ?>]" value="<?php echo $item['Quantity']; ?>" min="1" style="width: 60px;">
                        </td>
                        <td>R<?php echo number_format($subtotal, 2); ?></td>
                        <td><a href="?remove=<?php echo $item['CartID']; ?>" onclick="return confirm('Remove item?')" style="color: red;">Remove</a></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
            
            <div style="text-align: right; margin-top: 2rem;">
                <button type="submit" name="update_cart" class="btn btn-secondary">Update Cart</button>
                <div style="margin: 1rem 0;">
                    <strong>Total: R<?php echo number_format($total, 2); ?></strong>
                </div>
                <a href="checkout.php" class="btn btn-primary">Proceed to Checkout</a>
            </div>
        </form>
    <?php else: ?>
        <p style="text-align: center;">Your cart is empty. <a href="browse.php">Start shopping</a></p>
    <?php endif; ?>
</main>

<?php include 'includes/footer.php'; ?>