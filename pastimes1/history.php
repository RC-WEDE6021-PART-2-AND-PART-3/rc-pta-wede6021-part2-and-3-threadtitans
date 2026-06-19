<?php

include 'config/DBConn.php';
include 'includes/header.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$ordersQuery = "SELECT o.*, 
                (SELECT COUNT(*) FROM tblOrderLines WHERE OrderID = o.OrderID) as item_count
                FROM tblOrders o 
                WHERE o.UserID = $user_id 
                ORDER BY o.OrderDate DESC";
$orders = mysqli_query($conn, $ordersQuery);

$totalQuery = "SELECT SUM(TotalAmount) as grand_total FROM tblOrders WHERE UserID = $user_id AND OrderStatus != 'cancelled'";
$totalResult = mysqli_query($conn, $totalQuery);
$grand_total = mysqli_fetch_assoc($totalResult)['grand_total'] ?? 0;
?>

<style>
    .history-container {
        max-width: 1000px;
        margin: 20px auto;
        padding: 0 20px;
    }
    .history-table {
        width: 100%;
        border-collapse: collapse;
        background: white;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 3px 15px rgba(0,0,0,0.08);
    }
    .history-table th {
        background: #2c3e50;
        color: white;
        padding: 12px 15px;
        text-align: left;
    }
    .history-table td {
        padding: 12px 15px;
        border-bottom: 1px solid #eee;
    }
    .history-table tr:hover {
        background: #f8f9fa;
    }
    .grand-total-row {
        background: #f8f9fa;
        font-weight: bold;
        font-size: 1.1rem;
    }
    .grand-total-row td {
        border-top: 3px solid #2c3e50;
        padding: 15px;
    }
    .status-pending { color: #f39c12; }
    .status-processing { color: #3498db; }
    .status-shipped { color: #27ae60; }
    .status-delivered { color: #27ae60; }
    .status-cancelled { color: #e74c3c; }
    .no-orders {
        text-align: center;
        padding: 50px;
    }
</style>

<div class="history-container">
    <h1>📋 Purchase History</h1>
    <p><a href="shop.php">← Continue Shopping</a></p>

    <?php if(mysqli_num_rows($orders) > 0): ?>
        <table class="history-table">
            <thead>
                <tr>
                    <th>Order #</th>
                    <th>Date</th>
                    <th>Items</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Payment</th>
                </tr>
            </thead>
            <tbody>
                <?php while($order = mysqli_fetch_assoc($orders)): ?>
                    <tr>
                        <td><strong><?php echo $order['OrderNumber']; ?></strong></td>
                        <td><?php echo date('M d, Y', strtotime($order['OrderDate'])); ?></td>
                        <td><?php echo $order['item_count']; ?></td>
                        <td>R<?php echo number_format($order['TotalAmount'], 2); ?></td>
                        <td class="status-<?php echo $order['OrderStatus']; ?>">
                            <?php echo ucfirst($order['OrderStatus']); ?>
                        </td>
                        <td><?php echo str_replace('_', ' ', ucfirst($order['PaymentMethod'])); ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
            <tfoot>
                <tr class="grand-total-row">
                    <td colspan="3" style="text-align: right;">TOTAL SPENT:</td>
                    <td colspan="3">R<?php echo number_format($grand_total, 2); ?></td>
                </tr>
            </tfoot>
        </table>
    <?php else: ?>
        <div class="no-orders">
            <h2>No purchase history</h2>
            <p>You haven't made any purchases yet.</p>
            <a href="shop.php" class="btn btn-primary">Start Shopping</a>
        </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>