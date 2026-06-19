<?php
// add-item.php - Add new item with image upload
include 'config/DBConn.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'seller') {
    header("Location: login.php");
    exit();
}

$error = '';
$success = '';

$catQuery = "SELECT * FROM tblcategories";
$categories = mysqli_query($conn, $catQuery);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $product_name = mysqli_real_escape_string($conn, $_POST['product_name']);
    $category_id = intval($_POST['category_id']);
    $price = floatval($_POST['price']);
    $original_price = floatval($_POST['original_price']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $condition = mysqli_real_escape_string($conn, $_POST['condition']);
    $size = mysqli_real_escape_string($conn, $_POST['size']);
    $brand = mysqli_real_escape_string($conn, $_POST['brand']);
    $seller_id = $_SESSION['user_id'];
    
    // ============================================
    // IMAGE UPLOAD HANDLER
    // ============================================
    $image_path = '';
    if(isset($_FILES['product_image']) && $_FILES['product_image']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $filename = $_FILES['product_image']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        if(in_array($ext, $allowed)) {
            $new_filename = time() . '_' . rand(1000, 9999) . '.' . $ext;
            
            // Create uploads folder if not exists
            if(!is_dir('uploads/products')) {
                mkdir('uploads/products', 0777, true);
            }
            
            $upload_path = 'uploads/products/' . $new_filename;
            
            if(move_uploaded_file($_FILES['product_image']['tmp_name'], $upload_path)) {
                $image_path = $upload_path;
            }
        }
    }
    
    $query = "INSERT INTO tblproducts (SellerID, ProductName, CategoryID, Price, OriginalPrice, Description, `Condition`, Size, Brand, ImagePath, Status) 
              VALUES ($seller_id, '$product_name', $category_id, $price, " . ($original_price ?: 'NULL') . ", '$description', '$condition', '$size', '$brand', " . ($image_path ? "'$image_path'" : "NULL") . ", 'pending')";
    
    if(mysqli_query($conn, $query)) {
        $success = "Item submitted successfully! It will be visible after admin approval.";
    } else {
        $error = "Error: " . mysqli_error($conn);
    }
}

include 'includes/header.php';
?>

<style>
    .upload-preview {
        margin-top: 10px;
        max-width: 200px;
        display: none;
    }
    .upload-preview img {
        width: 100%;
        border-radius: 8px;
        border: 2px solid #27ae60;
    }
</style>

<main>
    <div class="form-container" style="max-width: 600px;">
        <h2 style="text-align: center;">📦 Sell Your Item</h2>
        <p style="text-align: center; color: #7f8c8d;">All items are reviewed before being listed</p>
        
        <?php if($error): ?>
            <div class="alert alert-error">❌ <?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if($success): ?>
            <div class="alert alert-success">✅ <?php echo $success; ?> <a href="dashboard.php">Go to Dashboard</a></div>
        <?php endif; ?>
        
        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label>Product Name *</label>
                <input type="text" name="product_name" required>
            </div>
            
            <div class="form-group">
                <label>Category *</label>
                <select name="category_id" required>
                    <option value="">Select Category</option>
                    <?php while($cat = mysqli_fetch_assoc($categories)): ?>
                        <option value="<?php echo $cat['CategoryID']; ?>"><?php echo $cat['CategoryName']; ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label>Brand</label>
                <input type="text" name="brand" placeholder="e.g., Nike, Gucci, Levi's">
            </div>
            
            <div class="form-group">
                <label>Selling Price (R) *</label>
                <input type="number" step="0.01" name="price" required>
            </div>
            
            <div class="form-group">
                <label>Original Price (R) - Optional</label>
                <input type="number" step="0.01" name="original_price">
            </div>
            
            <div class="form-group">
                <label>Condition *</label>
                <select name="condition" required>
                    <option value="new">New with tags</option>
                    <option value="like-new">Like New</option>
                    <option value="good">Good</option>
                    <option value="fair">Fair</option>
                </select>
            </div>
            
            <div class="form-group">
                <label>Size</label>
                <input type="text" name="size" placeholder="e.g., S, M, L, XL, 32x32, US 10">
            </div>
            
            <div class="form-group">
                <label>Description *</label>
                <textarea name="description" rows="5" required placeholder="Describe your item in detail..."></textarea>
            </div>
            
            <div class="form-group">
                <label>Product Image</label>
                <input type="file" name="product_image" accept="image/*" id="imageInput">
                <div class="upload-preview" id="imagePreview">
                    <img src="" alt="Preview">
                </div>
                <p style="font-size: 0.8rem; color: #7f8c8d; margin-top: 5px;">Allowed: JPG, PNG, GIF, WEBP | Max size: 5MB</p>
            </div>
            
            <button type="submit" class="btn-submit">Submit for Review</button>
        </form>
    </div>
</main>

<script>
    // Image preview
    document.getElementById('imageInput').addEventListener('change', function(e) {
        const preview = document.getElementById('imagePreview');
        const img = preview.querySelector('img');
        const file = e.target.files[0];
        
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                img.src = e.target.result;
                preview.style.display = 'block';
            }
            reader.readAsDataURL(file);
        } else {
            preview.style.display = 'none';
        }
    });
</script>

<?php include 'includes/footer.php'; ?>