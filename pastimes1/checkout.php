<?php

include 'config/DBConn.php';
include 'functions/cart_functions.php';
include 'includes/header.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php?redirect=checkout");
    exit();
}

$user_id = $_SESSION['user_id'];
$cartItems = GetCartItems($conn, $user_id);

if (mysqli_num_rows($cartItems) == 0) {
    header("Location: cart.php");
    exit();
}

$total = GetCartTotal($conn, $user_id);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $payment_method = mysqli_real_escape_string($conn, $_POST['payment_method']);
    
    $result = Checkout($conn, $user_id, $address, $payment_method);
    
    if ($result) {
        $_SESSION['last_order_num'] = $result['order_num'];
        $_SESSION['last_session_id'] = $result['session_id'];
        $_SESSION['last_order_id'] = $result['order_id'];
        header("Location: checkout-complete.php");
        exit();
    } else {
        $error = "Checkout failed. Please try again.";
    }
}

include 'includes/header.php';
?>

<style>
    .checkout-container {
        max-width: 800px;
        margin: 20px auto;
        padding: 0 20px;
    }
    .checkout-box {
        background: white;
        padding: 30px;
        border-radius: 10px;
        box-shadow: 0 3px 15px rgba(0,0,0,0.08);
    }
    .order-summary {
        background: #f8f9fa;
        padding: 20px;
        border-radius: 8px;
        margin: 20px 0;
    }
    .order-item {
        display: flex;
        justify-content: space-between;
        padding: 8px 0;
        border-bottom: 1px solid #eee;
    }
    .grand-total {
        font-size: 1.3rem;
        font-weight: bold;
        color: #e74c3c;
        padding-top: 15px;
    }
    .session-info {
        background: #d1ecf1;
        padding: 15px;
        border-radius: 8px;
        margin: 15px 0;
        font-family: monospace;
    }
</style>

<div class="checkout-container">
    <h1>📋 Checkout</h1>
    
    <?php if(isset($error)): ?>
        <div class="alert alert-error">❌ <?php echo $error; ?></div>
    <?php endif; ?>
    
    <div class="checkout-box">
        <h2>Order Summary</h2>
        
        <div class="order-summary">
            <?php 
            mysqli_data_seek($cartItems, 0);
            while($item = mysqli_fetch_assoc($cartItems)): 
            ?>
                <div class="order-item">
                    <span><?php echo htmlspecialchars($item['ProductName']); ?> × <?php echo $item['Quantity']; ?></span>
                    <span>R<?php echo number_format($item['Price'] * $item['Quantity'], 2); ?></span>
                </div>
            <?php endwhile; ?>
            
            <div class="order-item grand-total">
                <span>Total</span>
                <span>R<?php echo number_format($total, 2); ?></span>
            </div>
        </div>
        
        <div class="session-info">
            <strong>Session ID:</strong> <?php echo session_id(); ?>
        </div>
        
        <form method="POST">
            <div class="form-group">
                <label>Shipping Address *</label>
                <textarea name="address" rows="3" required placeholder="Enter your full delivery address" 
                          style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;"></textarea>
            </div>
            
            <div class="form-group">
                <label>Payment Method *</label>
                <select name="payment_method" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
                    <option value="credit_card">💳 Credit Card</option>
                    <option value="paypal">💰 PayPal</option>
                    <option value="bank_transfer">🏦 Bank Transfer</option>
                    <option value="cod">💵 Cash on Delivery</option>
                </select>
            </div>
            
            <button type="submit" class="btn btn-primary" style="width: 100%; padding: 15px; font-size: 1.1rem;">
                Confirm Purchase - R<?php echo number_format($total, 2); ?>
            </button>
        </form>
    </div>
</div>

<?php include 'includes/footer.php'; ?>