<?php
// shop.php - Browse products with images
include 'config/DBConn.php';
include 'functions/cart_functions.php';
include 'includes/header.php';

// Get category filter
$category_filter = isset($_GET['category']) ? intval($_GET['category']) : 0;

// Handle Add to Cart
if (isset($_POST['add_to_cart']) && isset($_SESSION['user_id'])) {
    $clothe_id = intval($_POST['clothe_id']);
    $quantity = intval($_POST['quantity'] ?? 1);
    AddItem($conn, $_SESSION['user_id'], $clothe_id, $quantity);
    $message = "✅ Item added to cart!";
}

// Get all approved items with filters
$itemsQuery = "SELECT c.*, u.Username as SellerName, cat.CategoryName 
               FROM tblproducts c 
               JOIN tblusers u ON c.SellerID = u.UserID
               JOIN tblcategories cat ON c.CategoryID = cat.CategoryID
               WHERE c.Status = 'approved' AND c.Quantity > 0";

if ($category_filter > 0) {
    $itemsQuery .= " AND c.CategoryID = $category_filter";
}

$itemsQuery .= " ORDER BY c.CreatedAt DESC";
$items = mysqli_query($conn, $itemsQuery);

// Get cart count
$cart_count = 0;
if (isset($_SESSION['user_id'])) {
    $cartResult = mysqli_query($conn, "SELECT SUM(Quantity) as total FROM tblcart WHERE UserID = {$_SESSION['user_id']}");
    $cart_count = mysqli_fetch_assoc($cartResult)['total'] ?? 0;
}

// Get categories for filter
$catQuery = "SELECT * FROM tblcategories ORDER BY CategoryName";
$categories = mysqli_query($conn, $catQuery);
?>

<style>
    .shop-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        margin-bottom: 30px;
    }
    .cart-badge {
        background: #e74c3c;
        color: white;
        padding: 10px 20px;
        border-radius: 50px;
        text-decoration: none;
        font-weight: bold;
    }
    .cart-badge:hover {
        background: #c0392b;
    }
    .filter-bar {
        background: white;
        padding: 15px 20px;
        border-radius: 10px;
        margin-bottom: 30px;
        display: flex;
        gap: 15px;
        flex-wrap: wrap;
        align-items: center;
        box-shadow: 0 2px 10px rgba(0,0,0,0.08);
    }
    .filter-bar select,
    .filter-bar input {
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 5px;
    }
    .btn-add-cart {
        background: #27ae60;
        color: white;
        border: none;
        padding: 8px 15px;
        border-radius: 5px;
        cursor: pointer;
        font-weight: 600;
    }
    .btn-add-cart:hover {
        background: #219a52;
    }
    .badge {
        display: inline-block;
        padding: 3px 10px;
        border-radius: 20px;
        font-size: 0.7rem;
        font-weight: 600;
        background: #3498db;
        color: white;
    }
</style>

<main style="max-width: 1200px; margin: 20px auto; padding: 0 20px;">
    <div class="shop-header">
        <h1>🛍️ Our Collection</h1>
        <div>
            <a href="cart.php" class="cart-badge">
                🛒 Cart (<?php echo $cart_count; ?> items)
            </a>
        </div>
    </div>

    <?php if(isset($message)): ?>
        <div class="alert alert-success"><?php echo $message; ?></div>
    <?php endif; ?>

    <!-- Filter Bar -->
    <div class="filter-bar">
        <form method="GET" action="" style="display: flex; gap: 15px; flex-wrap: wrap; width: 100%;">
            <select name="category" style="padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
                <option value="0">All Categories</option>
                <?php 
                mysqli_data_seek($categories, 0);
                while($cat = mysqli_fetch_assoc($categories)): 
                    $selected = ($category_filter == $cat['CategoryID']) ? 'selected' : '';
                ?>
                    <option value="<?php echo $cat['CategoryID']; ?>" <?php echo $selected; ?>>
                        <?php echo htmlspecialchars($cat['CategoryName']); ?>
                    </option>
                <?php endwhile; ?>
            </select>
            <button type="submit" class="btn btn-primary">Filter</button>
            <a href="shop.php" class="btn btn-secondary" style="color: #333; border-color: #ddd;">Reset</a>
        </form>
    </div>

    <?php if(mysqli_num_rows($items) > 0): ?>
        <div class="product-grid">
            <?php while($item = mysqli_fetch_assoc($items)): 
                // Check if image exists
                $imagePath = !empty($item['ImagePath']) ? $item['ImagePath'] : 'images/placeholders/product.png';
                if (!file_exists($imagePath) && strpos($imagePath, 'http') !== 0) {
                    $imagePath = 'https://via.placeholder.com/300x250/3498db/fff?text=Pastimes';
                }
            ?>
            <div class="product-card">
                <img src="<?php echo $imagePath; ?>" 
                     alt="<?php echo htmlspecialchars($item['ProductName']); ?>" 
                     class="product-image"
                     onerror="this.src='https://via.placeholder.com/300x250/3498db/fff?text=Pastimes'">
                <div class="product-info">
                    <h3 class="product-title"><?php echo htmlspecialchars($item['ProductName']); ?></h3>
                    <p><span class="badge"><?php echo htmlspecialchars($item['CategoryName']); ?></span></p>
                    <p class="product-price">
                        R<?php echo number_format($item['Price'], 2); ?>
                        <?php if($item['OriginalPrice']): ?>
                            <span class="product-original-price">R<?php echo number_format($item['OriginalPrice'], 2); ?></span>
                        <?php endif; ?>
                    </p>
                    <p class="product-seller">👤 <?php echo htmlspecialchars($item['SellerName']); ?></p>
                    <p class="product-stock">📦 <?php echo $item['Quantity']; ?> in stock</p>
                    <p><small>Brand: <?php echo htmlspecialchars($item['Brand'] ?: 'Not specified'); ?></small></p>
                    
                    <?php if(isset($_SESSION['user_id']) && $_SESSION['user_type'] != 'admin'): ?>
                        <form method="POST" style="display: flex; gap: 10px; align-items: center; margin-top: 10px;">
                            <input type="hidden" name="clothe_id" value="<?php echo $item['ProductID']; ?>">
                            <input type="number" name="quantity" value="1" min="1" max="<?php echo $item['Quantity']; ?>" 
                                   style="width: 60px; padding: 8px; border: 1px solid #ddd; border-radius: 5px; text-align: center;">
                            <button type="submit" name="add_to_cart" class="btn-add-cart">Add to Cart</button>
                        </form>
                    <?php elseif(!isset($_SESSION['user_id'])): ?>
                        <p><small><a href="login.php">Login</a> to purchase</small></p>
                    <?php endif; ?>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <p style="text-align: center; padding: 50px;">No items available in this category. Check back soon!</p>
    <?php endif; ?>
</main>

<?php include 'includes/footer.php'; ?>