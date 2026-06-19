<?php

include 'config/DBConn.php';
include 'includes/header.php';

if (!isset($_SESSION['last_order_num'])) {
    header("Location: shop.php");
    exit();
}

$orderNum = $_SESSION['last_order_num'];
$sessionId = $_SESSION['last_session_id'];
$orderId = $_SESSION['last_order_id'];

unset($_SESSION['last_order_num']);
unset($_SESSION['last_session_id']);
unset($_SESSION['last_order_id']);
?>

<style>
    .confirmation {
        text-align: center;
        padding: 50px 20px;
        max-width: 600px;
        margin: 0 auto;
    }
    .confirmation .icon {
        font-size: 5rem;
        margin-bottom: 20px;
    }
    .confirmation h1 {
        color: #27ae60;
        margin-bottom: 20px;
    }
    .order-details {
        background: #f8f9fa;
        padding: 20px;
        border-radius: 10px;
        margin: 20px 0;
        font-family: monospace;
        text-align: left;
    }
    .order-details p {
        padding: 5px 0;
        border-bottom: 1px solid #eee;
    }
</style>

<div class="confirmation">
    <div class="icon">✅</div>
    <h1>Order Confirmed!</h1>
    <p>Thank you for your purchase. Your order has been placed successfully.</p>
    
    <div class="order-details">
        <p><strong>📦 Order Number:</strong> <?php echo $orderNum; ?></p>
        <p><strong>🆔 Session ID:</strong> <?php echo $sessionId; ?></p>
        <p><strong>📅 Date:</strong> <?php echo date('Y-m-d H:i:s'); ?></p>
    </div>
    
    <div style="margin-top: 30px;">
        <a href="shop.php" class="btn btn-primary">Continue Shopping</a>
        <a href="history.php" class="btn btn-secondary">View Order History</a>
    </div>
</div>

<?php include 'includes/footer.php'; ?>