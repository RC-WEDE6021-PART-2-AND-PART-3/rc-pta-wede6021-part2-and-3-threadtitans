<?php

include 'config/DBConn.php';
include 'includes/header.php';

$featuredQuery = "SELECT p.*, u.Username as SellerName, c.CategoryName 
                  FROM tblProducts p
                  JOIN tblUsers u ON p.SellerID = u.UserID
                  JOIN tblCategories c ON p.CategoryID = c.CategoryID
                  WHERE p.Status = 'approved'
                  ORDER BY p.ProductID DESC LIMIT 6";
$featuredResult = mysqli_query($conn, $featuredQuery);
?>

<main>
    <div class="hero">
        <div class="hero-content">
            <h1>Buy & Sell Second-Hand <br><span style="color: #e74c3c;">Branded Clothing</span></h1>
            <p>Sustainable fashion at affordable prices. Join our community today!</p>
            <div class="hero-buttons">
                <a href="browse.php" class="btn btn-primary">Browse Items</a>
                <?php if(isset($_SESSION['user_id'])): ?>
                    <a href="add-item.php" class="btn btn-secondary">Sell Now</a>
                <?php else: ?>
                    <a href="register.php" class="btn btn-secondary">Sell Now</a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="featured-section">
        <h2 class="section-title">Featured Items</h2>
        <div class="product-grid">
            <?php while($product = mysqli_fetch_assoc($featuredResult)): ?>
                <div class="product-card">
                    <img src="<?php echo !empty($product['ImagePath']) ? $product['ImagePath'] : 'https://via.placeholder.com/300x250?text=Pastimes'; ?>" alt="<?php echo htmlspecialchars($product['ProductName']); ?>" class="product-image">
                    <div class="product-info">
                        <h3 class="product-title"><?php echo htmlspecialchars($product['ProductName']); ?></h3>
                        <p class="product-price">
                            R<?php echo number_format($product['Price'], 2); ?>
                            <?php if($product['OriginalPrice']): ?>
                                <span class="product-original-price">R<?php echo number_format($product['OriginalPrice'], 2); ?></span>
                            <?php endif; ?>
                        </p>
                        <p><small>By: <?php echo htmlspecialchars($product['SellerName']); ?></small></p>
                        <a href="item-details.php?id=<?php echo $product['ProductID']; ?>" class="btn btn-accent" style="margin-top: 10px; display: inline-block;">View Details</a>
                    </div>
                </div>
            <?php endwhile; ?>
            
            <?php if(mysqli_num_rows($featuredResult) == 0): ?>
                <p>No products available yet. Check back soon!</p>
            <?php endif; ?>
        </div>
    </div>
    
    <div style="background-color: var(--light-color); padding: 3rem 2rem; text-align: center;">
        <div style="max-width: 800px; margin: 0 auto;">
            <h2>Why Choose Pastimes?</h2>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 2rem; margin-top: 2rem;">
                <div>
                    <h3>🛡️ Verified Sellers</h3>
                    <p>All sellers are verified before listing items</p>
                </div>
                <div>
                    <h3>💬 Secure Messaging</h3>
                    <p>Chat directly with sellers</p>
                </div>
                <div>
                    <h3>🛒 Easy Checkout</h3>
                    <p>Simple and secure payment process</p>
                </div>
                <div>
                    <h3>♻️ Sustainable</h3>
                    <p>Help reduce textile waste</p>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include 'includes/footer.php'; ?>