<?php
// index.php - Complete Homepage with Hero Banner
include 'config/DBConn.php';
include 'includes/header.php';

// ============================================
// HERO BANNER IMAGE SETUP
// ============================================

// Check if hero banner exists locally
$heroImage = 'images/banners/hero-banner.jpg';

// If the image doesn't exist locally, use an online fallback
if (!file_exists($heroImage)) {
    $heroImage = 'https://images.unsplash.com/photo-1483985988355-763728e1935b?w=1920&h=600&fit=crop';
}

// ============================================
// FEATURED PRODUCTS QUERY
// ============================================

$featuredQuery = "SELECT p.*, u.Username as SellerName, c.CategoryName 
                 FROM tblproducts p
                 JOIN tblusers u ON p.SellerID = u.UserID
                 JOIN tblcategories c ON p.CategoryID = c.CategoryID
                 WHERE p.Status = 'approved' AND p.Quantity > 0
                 ORDER BY p.ProductID DESC LIMIT 6";
$featuredResult = mysqli_query($conn, $featuredQuery);

// ============================================
// CATEGORIES QUERY FOR ICONS
// ============================================

$catQuery = "SELECT * FROM tblcategories ORDER BY CategoryName";
$categories = mysqli_query($conn, $catQuery);
?>

<style>
    /* Hero Section with dynamic background */
    .hero-section {
        background: linear-gradient(rgba(0,0,0,0.55), rgba(0,0,0,0.65)), 
                    url('<?php echo $heroImage; ?>');
        background-size: cover;
        background-position: center 35%;
        background-repeat: no-repeat;
        color: white;
        padding: 100px 40px;
        text-align: left;
        border-radius: 15px;
        margin: 20px 0;
        min-height: 500px;
        display: flex;
        align-items: center;
        position: relative;
        overflow: hidden;
    }

    .hero-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(90deg, rgba(0,0,0,0.7) 0%, rgba(0,0,0,0.3) 60%, rgba(0,0,0,0.1) 100%);
        border-radius: 15px;
    }

    .hero-content {
        position: relative;
        z-index: 1;
        max-width: 700px;
    }

    .hero-title {
        font-size: 4.5rem;
        font-weight: 900;
        letter-spacing: -3px;
        margin: 0;
        text-shadow: 0 4px 30px rgba(0,0,0,0.3);
    }

    .hero-title .dot {
        font-size: 4.5rem;
        font-weight: 900;
        color: #e74c3c;
        margin-left: 5px;
        text-shadow: 0 4px 30px rgba(231, 76, 60, 0.3);
    }

    .hero-tagline {
        font-size: 1.5rem;
        opacity: 0.95;
        margin-bottom: 25px;
        font-weight: 300;
        text-shadow: 0 2px 20px rgba(0,0,0,0.3);
    }

    .hero-tagline .highlight {
        color: #e74c3c;
        font-weight: 600;
    }

    .hero-badges {
        display: flex;
        gap: 15px;
        flex-wrap: wrap;
        margin-bottom: 30px;
    }

    .hero-badge {
        background: rgba(255,255,255,0.15);
        backdrop-filter: blur(10px);
        padding: 8px 20px;
        border-radius: 50px;
        font-size: 0.9rem;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        border: 1px solid rgba(255,255,255,0.1);
    }

    .hero-buttons {
        display: flex;
        gap: 15px;
        flex-wrap: wrap;
    }

    .btn-hero-primary {
        background: linear-gradient(135deg, #e74c3c, #c0392b);
        color: white;
        padding: 15px 45px;
        border-radius: 50px;
        text-decoration: none;
        font-weight: 600;
        transition: all 0.3s;
        display: inline-block;
        box-shadow: 0 4px 25px rgba(231, 76, 60, 0.4);
        border: none;
        cursor: pointer;
        font-size: 1.05rem;
    }

    .btn-hero-primary:hover {
        transform: scale(1.05);
        box-shadow: 0 6px 35px rgba(231, 76, 60, 0.5);
    }

    .btn-hero-secondary {
        background: rgba(255,255,255,0.15);
        backdrop-filter: blur(10px);
        color: white;
        padding: 15px 45px;
        border-radius: 50px;
        text-decoration: none;
        font-weight: 600;
        transition: all 0.3s;
        display: inline-block;
        border: 2px solid rgba(255,255,255,0.3);
        font-size: 1.05rem;
    }

    .btn-hero-secondary:hover {
        border-color: white;
        background: rgba(255,255,255,0.25);
    }

    .hero-stats {
        margin-top: 35px;
        display: flex;
        gap: 40px;
        flex-wrap: wrap;
    }

    .hero-stat {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .hero-stat-icon {
        font-size: 1.8rem;
    }

    .hero-stat-number {
        font-weight: 700;
        font-size: 1.1rem;
    }

    .hero-stat-label {
        font-size: 0.8rem;
        opacity: 0.8;
    }

    @media (max-width: 768px) {
        .hero-section {
            padding: 40px 20px;
            min-height: 350px;
            text-align: center;
        }
        .hero-title {
            font-size: 2.8rem;
        }
        .hero-title .dot {
            font-size: 2.8rem;
        }
        .hero-tagline {
            font-size: 1rem;
        }
        .hero-badges {
            justify-content: center;
        }
        .hero-buttons {
            justify-content: center;
        }
        .hero-stats {
            justify-content: center;
            gap: 20px;
        }
        .hero-stat {
            flex-direction: column;
            text-align: center;
        }
        .btn-hero-primary,
        .btn-hero-secondary {
            padding: 12px 30px;
            font-size: 0.95rem;
            width: 100%;
            text-align: center;
        }
    }
</style>

<main>
    <!-- ============================================ -->
    <!-- HERO BANNER                                 -->
    <!-- ============================================ -->
    
    <div class="hero-section">
        <div class="hero-overlay"></div>
        
        <div class="hero-content">
            <!-- Brand Name -->
            <div style="display: flex; align-items: center; flex-wrap: wrap;">
                <h1 class="hero-title">Pastimes</h1>
                <span class="dot">.</span>
            </div>
            
            <!-- Tagline -->
            <p class="hero-tagline">
                Second-Hand <span class="highlight">Branded</span> Clothing Marketplace
            </p>
            
            <!-- Feature Badges -->
            <div class="hero-badges">
                <span class="hero-badge">♻️ Sustainable</span>
                <span class="hero-badge">🏷️ Premium Brands</span>
                <span class="hero-badge">💰 Affordable</span>
            </div>
            
            <!-- Call to Action Buttons -->
            <div class="hero-buttons">
                <a href="shop.php" class="btn-hero-primary">🛍️ Start Shopping</a>
                
                <?php if(isset($_SESSION['user_id']) && $_SESSION['user_type'] == 'seller'): ?>
                    <a href="seller/seller_request.php" class="btn-hero-secondary">📦 Sell Your Items</a>
                <?php else: ?>
                    <a href="register.php" class="btn-hero-secondary">📦 Sell Now</a>
                <?php endif; ?>
            </div>
            
            <!-- Stats -->
            <div class="hero-stats">
                <div class="hero-stat">
                    <span class="hero-stat-icon">👥</span>
                    <div>
                        <div class="hero-stat-number">500+</div>
                        <div class="hero-stat-label">Happy Customers</div>
                    </div>
                </div>
                <div class="hero-stat">
                    <span class="hero-stat-icon">👕</span>
                    <div>
                        <div class="hero-stat-number">1000+</div>
                        <div class="hero-stat-label">Items Listed</div>
                    </div>
                </div>
                <div class="hero-stat">
                    <span class="hero-stat-icon">⭐</span>
                    <div>
                        <div class="hero-stat-number">4.8</div>
                        <div class="hero-stat-label">Average Rating</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- ============================================ -->
    <!-- SHOP BY CATEGORY SECTION                    -->
    <!-- ============================================ -->
    
    <?php if(mysqli_num_rows($categories) > 0): ?>
    <div style="max-width: 1200px; margin: 0 auto; padding: 0 20px;">
        <h2 class="section-title">🛍️ Shop by Category</h2>
        <div class="category-grid">
            <?php 
            mysqli_data_seek($categories, 0);
            while($cat = mysqli_fetch_assoc($categories)): 
                $iconPath = 'images/categories/' . strtolower(str_replace(' ', '_', $cat['CategoryName'])) . '.png';
                if (!file_exists($iconPath)) {
                    $iconPath = 'https://via.placeholder.com/60x60/3498db/fff?text=' . urlencode(substr($cat['CategoryName'], 0, 1));
                }
            ?>
            <a href="shop.php?category=<?php echo $cat['CategoryID']; ?>" class="category-card">
                <img src="<?php echo $iconPath; ?>" 
                     alt="<?php echo htmlspecialchars($cat['CategoryName']); ?>"
                     class="category-icon"
                     onerror="this.src='https://via.placeholder.com/60x60/3498db/fff?text=<?php echo urlencode(substr($cat['CategoryName'], 0, 1)); ?>'">
                <p class="category-name"><?php echo htmlspecialchars($cat['CategoryName']); ?></p>
            </a>
            <?php endwhile; ?>
        </div>
    </div>
    <?php endif; ?>
    
    <!-- ============================================ -->
    <!-- FEATURED PRODUCTS SECTION                   -->
    <!-- ============================================ -->
    
    <div style="max-width: 1200px; margin: 0 auto; padding: 0 20px;">
        <h2 class="section-title">🔥 Featured Items</h2>
        
        <?php if(mysqli_num_rows($featuredResult) > 0): ?>
            <div class="product-grid">
                <?php while($product = mysqli_fetch_assoc($featuredResult)): 
                    $imagePath = !empty($product['ImagePath']) ? $product['ImagePath'] : 'images/placeholders/product.png';
                    if (!file_exists($imagePath) && strpos($imagePath, 'http') !== 0) {
                        $imagePath = 'https://via.placeholder.com/300x250/3498db/fff?text=Pastimes';
                    }
                ?>
                <div class="product-card">
                    <img src="<?php echo $imagePath; ?>" 
                         alt="<?php echo htmlspecialchars($product['ProductName']); ?>" 
                         class="product-image"
                         onerror="this.src='https://via.placeholder.com/300x250/3498db/fff?text=Pastimes'">
                    <div class="product-info">
                        <h3 class="product-title"><?php echo htmlspecialchars($product['ProductName']); ?></h3>
                        <p>
                            <span class="product-category-badge"><?php echo htmlspecialchars($product['CategoryName']); ?></span>
                        </p>
                        <p class="product-price">
                            R<?php echo number_format($product['Price'], 2); ?>
                            <?php if($product['OriginalPrice']): ?>
                                <span class="product-original-price">R<?php echo number_format($product['OriginalPrice'], 2); ?></span>
                            <?php endif; ?>
                        </p>
                        <p><small>👤 <?php echo htmlspecialchars($product['SellerName']); ?></small></p>
                        <a href="item-details.php?id=<?php echo $product['ProductID']; ?>" class="btn-accent">View Details</a>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <p style="text-align: center; padding: 50px; color: #7f8c8d;">No featured items available at the moment. Check back soon!</p>
        <?php endif; ?>
    </div>
    
    <!-- ============================================ -->
    <!-- WHY CHOOSE PASTIMES                        -->
    <!-- ============================================ -->
    
    <div style="max-width: 1200px; margin: 40px auto; padding: 0 20px;">
        <h2 class="section-title">Why Choose Pastimes?</h2>
        
        <div class="shop-goals">
            <div class="goal-card">
                <div class="icon">♻️</div>
                <h3>Sustainable Fashion</h3>
                <p>Reduce textile waste by giving pre-loved clothing a second life</p>
            </div>
            <div class="goal-card">
                <div class="icon">💰</div>
                <h3>Affordable Prices</h3>
                <p>Get premium branded clothing at a fraction of the retail price</p>
            </div>
            <div class="goal-card">
                <div class="icon">🛡️</div>
                <h3>Verified Sellers</h3>
                <p>All sellers are verified to ensure quality and authenticity</p>
            </div>
            <div class="goal-card">
                <div class="icon">🌍</div>
                <h3>Community Driven</h3>
                <p>Join a community of fashion-conscious buyers and sellers</p>
            </div>
        </div>
    </div>
    
</main>

<?php include 'includes/footer.php'; ?>