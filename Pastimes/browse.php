<?php

include 'config/DBConn.php';
include 'includes/header.php';

$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$category = isset($_GET['category']) ? intval($_GET['category']) : 0;
$condition = isset($_GET['condition']) ? mysqli_real_escape_string($conn, $_GET['condition']) : '';

$query = "SELECT p.*, u.Username as SellerName, c.CategoryName 
          FROM tblProducts p
          JOIN tblUsers u ON p.SellerID = u.UserID
          JOIN tblCategories c ON p.CategoryID = c.CategoryID
          WHERE p.Status = 'approved'";

if($search) {
    $query .= " AND (p.ProductName LIKE '%$search%' OR p.Description LIKE '%$search%' OR p.Brand LIKE '%$search%')";
}
if($category > 0) {
    $query .= " AND p.CategoryID = $category";
}
if($condition) {
    $query .= " AND p.Condition = '$condition'";
}

$query .= " ORDER BY p.CreatedAt DESC";
$result = mysqli_query($conn, $query);

$catQuery = "SELECT * FROM tblCategories";
$categories = mysqli_query($conn, $catQuery);
?>

<main>
    <div style="max-width: 1200px; margin: 2rem auto; padding: 0 2rem;">
        <h1 class="section-title">Browse Items</h1>
        
        <div style="background: white; padding: 1.5rem; border-radius: 10px; margin-bottom: 2rem;">
            <form method="GET" action="">
                <div style="display: grid; grid-template-columns: 1fr auto auto auto; gap: 1rem;">
                    <input type="text" name="search" placeholder="Search by name, brand, or description..." value="<?php echo htmlspecialchars($search); ?>" style="padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
                    
                    <select name="category" style="padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
                        <option value="0">All Categories</option>
                        <?php while($cat = mysqli_fetch_assoc($categories)): ?>
                            <option value="<?php echo $cat['CategoryID']; ?>" <?php echo $category == $cat['CategoryID'] ? 'selected' : ''; ?>>
                                <?php echo $cat['CategoryName']; ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                    
                    <select name="condition" style="padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
                        <option value="">All Conditions</option>
                        <option value="new" <?php echo $condition == 'new' ? 'selected' : ''; ?>>New</option>
                        <option value="like-new" <?php echo $condition == 'like-new' ? 'selected' : ''; ?>>Like New</option>
                        <option value="good" <?php echo $condition == 'good' ? 'selected' : ''; ?>>Good</option>
                        <option value="fair" <?php echo $condition == 'fair' ? 'selected' : ''; ?>>Fair</option>
                    </select>
                    
                    <button type="submit" class="btn btn-primary">Search</button>
                </div>
            </form>
        </div>
    
        <p>Found <?php echo mysqli_num_rows($result); ?> items</p>
        
        <div class="product-grid" style="margin-top: 2rem;">
            <?php while($product = mysqli_fetch_assoc($result)): ?>
                <div class="product-card">
                    <img src="<?php echo !empty($product['ImagePath']) ? $product['ImagePath'] : 'https://via.placeholder.com/300x250?text=Pastimes'; ?>" alt="<?php echo htmlspecialchars($product['ProductName']); ?>" class="product-image">
                    <div class="product-info">
                        <h3 class="product-title"><?php echo htmlspecialchars($product['ProductName']); ?></h3>
                        <p><small><?php echo $product['CategoryName']; ?> | <?php echo ucfirst($product['Condition']); ?></small></p>
                        <p class="product-price">
                            R<?php echo number_format($product['Price'], 2); ?>
                            <?php if($product['OriginalPrice']): ?>
                                <span class="product-original-price">R<?php echo number_format($product['OriginalPrice'], 2); ?></span>
                            <?php endif; ?>
                        </p>
                        <p><small>Seller: <?php echo htmlspecialchars($product['SellerName']); ?></small></p>
                        <a href="item-details.php?id=<?php echo $product['ProductID']; ?>" class="btn btn-accent" style="margin-top: 10px; width: 100%; text-align: center;">View Details</a>
                    </div>
                </div>
            <?php endwhile; ?>
            
            <?php if(mysqli_num_rows($result) == 0): ?>
                <p style="text-align: center;">No items found. Try adjusting your search.</p>
            <?php endif; ?>
        </div>
    </div>
</main>

<?php include 'includes/footer.php'; ?>