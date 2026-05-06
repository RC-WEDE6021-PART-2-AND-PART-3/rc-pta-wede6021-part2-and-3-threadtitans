<?php
include 'config/DBConn.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$cartQuery = "SELECT c.*, p.ProductName, p.Price, p.ProductID 
              FROM tblCart c
              JOIN tblProducts p ON c.ProductID = p.ProductID
              WHERE c.UserID = $user_id";
$cartResult = mysqli_query($conn, $cartQuery);

if(mysqli_num_rows($cartResult) == 0) {
    header("Location: cart.php");
    exit();
}

$total = 0;
while($item = mysqli_fetch_assoc($cartResult)) {
    $total += $item['Price'] * $item['Quantity'];
}

if($_SERVER["REQUEST_METHOD"] == "POST") {
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $payment_method = mysqli_real_escape_string($conn, $_POST['payment_method']);
    
    $orderQuery = "INSERT INTO tblOrders (UserID, TotalAmount, ShippingAddress, PaymentMethod, OrderStatus) 
                   VALUES ($user_id, $total, '$address', '$payment_method', 'pending')";
    mysqli_query($conn, $orderQuery);
    $order_id = mysqli_insert_id($conn);
    
    mysqli_data_seek($cartResult, 0);
    while($item = mysqli_fetch_assoc($cartResult)) {
        $orderItemQuery = "INSERT INTO tblOrderItems (OrderID, ProductID, Quantity, Price) 
                           VALUES ($order_id, {$item['ProductID']}, {$item['Quantity']}, {$item['Price']})";
        mysqli_query($conn, $orderItemQuery);
        
        mysqli_query($conn, "UPDATE tblProducts SET Status = 'sold' WHERE ProductID = {$item['ProductID']}");
    }
    
    mysqli_query($conn, "DELETE FROM tblCart WHERE UserID = $user_id");
    
    header("Location: order-confirmation.php?id=$order_id");
    exit();
}

include 'includes/header.php';
?>

<main style="max-width: 800px; margin: 2rem auto; padding: 0 2rem;">
    <h1 class="section-title">Checkout</h1>
    
    <div style="background: white; padding: 2rem; border-radius: 10px;">
        <h3>Order Summary</h3>
        <table class="cart-table">
            <tr><th>Item</th><th>Qty</th><th>Price</th></tr>
            <?php mysqli_data_seek($cartResult, 0);
            while($item = mysqli_fetch_assoc($cartResult)): ?>
            <tr>
                <td><?php echo htmlspecialchars($item['ProductName']); ?></td>
                <td><?php echo $item['Quantity']; ?></td>
                <td>R<?php echo number_format($item['Price'] * $item['Quantity'], 2); ?></td>
            </tr>
            <?php endwhile; ?>
            <tr><th colspan="2">Total</th><th>R<?php echo number_format($total, 2); ?></th></tr>
        </table>
        
        <form method="POST" style="margin-top: 2rem;">
            <div class="form-group">
                <label>Shipping Address *</label>
                <textarea name="address" rows="3" required placeholder="Enter your full address"></textarea>
            </div>
            
            <div class="form-group">
                <label>Payment Method *</label>
                <select name="payment_method" required>
                    <option value="credit_card">Credit Card</option>
                    <option value="paypal">PayPal</option>
                    <option value="bank_transfer">Bank Transfer</option>
                    <option value="cod">Cash on Delivery</option>
                </select>
            </div>
            
            <button type="submit" class="btn-submit">Confirm Purchase</button>
        </form>
    </div>
</main>

<?php include 'includes/footer.php'; ?>