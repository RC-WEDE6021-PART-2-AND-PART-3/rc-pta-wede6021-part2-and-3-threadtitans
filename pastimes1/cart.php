<?php
// cart.php - Shopping Cart with images
include 'config/DBConn.php';
include 'functions/cart_functions.php';
include 'includes/header.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Handle RemoveItem
if (isset($_GET['remove'])) {
    $cart_id = intval($_GET['remove']);
    RemoveItem($conn, $cart_id, $user_id);
    header("Location: cart.php");
    exit();
}

// Handle Edit quantity
if (isset($_POST['update_cart'])) {
    foreach ($_POST['quantity'] as $cart_id => $qty) {
        $qty = max(1, intval($qty));
        mysqli_query($conn, "UPDATE tblcart SET Quantity = $qty WHERE CartID = $cart_id AND UserID = $user_id");
    }
    header("Location: cart.php");
    exit();
}

// Handle EmptyCart
if (isset($_GET['empty'])) {
    EmptyCart($conn, $user_id);
    header("Location: cart.php");
    exit();
}

// Get cart items
$cartItems = GetCartItems($conn, $user_id);
$total = GetCartTotal($conn, $user_id);
$itemCount = mysqli_num_rows($cartItems);
?>

<style>
    .cart-container {
        max-width: 1000px;
        margin: 20px auto;
        padding: 0 20px;
    }
    .cart-table {
        width: 100%;
        border-collapse: collapse;
        background: white;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 3px 15px rgba(0,0,0,0.08);
    }
    .cart-table th {
        background: #2c3e50;
        color: white;
        padding: 15px;
        text-align: left;
    }
    .cart-table td {
        padding: 15px;
        border-bottom: 1px solid #eee;
        vertical-align: middle;
    }
    .cart-table tr:hover {
        background: #f8f9fa;
    }
    .cart-actions {
        display: flex;
        gap: 10px;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        margin-top: 20px;
        padding: 20px;
        background: white;
        border-radius: 10px;
        box-shadow: 0 3px 15px rgba(0,0,0,0.08);
    }
    .total-amount {
        font-size: 1.5rem;
        font-weight: bold;
        color: #e74c3c;
    }
    .empty-cart {
        text-align: center;
        padding: 50px;
    }
    .empty-cart .icon {
        font-size: 4rem;
    }
    .cart-thumbnail {
        width: 60px;
        height: 60px;
        object-fit: cover;
        border-radius: 8px;
        vertical-align: middle;
        margin-right: 10px;
    }
</style>

<div class="cart-container">
    <h1>🛒 Your Shopping Cart</h1>
    <p><a href="shop.php">← Continue Shopping</a></p>

    <?php if($itemCount > 0): ?>
        <form method="POST">
            <table class="cart-table">
                <thead>
                    <tr>
                        <th>Item</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Subtotal</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($item = mysqli_fetch_assoc($cartItems)): 
                        $subtotal = $item['Price'] * $item['Quantity'];
                        $imagePath = !empty($item['ImagePath']) ? $item['ImagePath'] : 'images/placeholders/product.png';
                    ?>
                    <tr>
                        <td>
                            <img src="<?php echo $imagePath; ?>" 
                                 alt="<?php echo htmlspecialchars($item['ProductName']); ?>"
                                 class="cart-thumbnail"
                                 onerror="this.src='https://via.placeholder.com/60x60/3498db/fff?text=Pastimes'">
                            <?php echo htmlspecialchars($item['ProductName']); ?>
                        </td>
                        <td>R<?php echo number_format($item['Price'], 2); ?></td>
                        <td>
                            <input type="number" name="quantity[<?php echo $item['CartID']; ?>]" 
                                   value="<?php echo $item['Quantity']; ?>" min="1" 
                                   style="width: 60px; padding: 5px; border: 1px solid #ddd; border-radius: 5px; text-align: center;">
                        </td>
                        <td>R<?php echo number_format($subtotal, 2); ?></td>
                        <td>
                            <a href="?remove=<?php echo $item['CartID']; ?>" class="btn btn-danger btn-sm" 
                               onclick="return confirm('Remove this item?')" style="background: #e74c3c; color: white; padding: 5px 12px; border-radius: 5px; text-decoration: none; font-size: 0.8rem;">Remove</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>

            <div class="cart-actions">
                <div>
                    <button type="submit" name="update_cart" class="btn btn-secondary" style="background: #3498db; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer;">Update Quantities</button>
                    <a href="?empty=1" class="btn btn-danger" onclick="return confirm('Empty your cart?')" style="background: #e74c3c; color: white; padding: 10px 20px; border-radius: 5px; text-decoration: none;">Empty Cart</a>
                </div>
                <div>
                    <span class="total-amount">Total: R<?php echo number_format($total, 2); ?></span>
                    <a href="checkout.php" class="btn btn-primary" style="background: #27ae60; color: white; padding: 10px 30px; border-radius: 5px; text-decoration: none; margin-left: 10px;">Proceed to Checkout</a>
                </div>
            </div>
        </form>
    <?php else: ?>
        <div class="empty-cart">
            <div class="icon">🛒</div>
            <h2>Your cart is empty</h2>
            <p>Browse our collection and add items you love!</p>
            <a href="shop.php" class="btn btn-primary" style="background: #e74c3c; color: white; padding: 10px 30px; border-radius: 5px; text-decoration: none;">Start Shopping</a>
        </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>