<?php

include '../config/DBConn.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'seller') {
    header("Location: ../login.php");
    exit();
}

$error = '';
$success = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $product_name = mysqli_real_escape_string($conn, $_POST['product_name']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $brand = mysqli_real_escape_string($conn, $_POST['brand']);
    $seller_id = $_SESSION['user_id'];
    
    $image_path = '';
    if(isset($_FILES['product_image']) && $_FILES['product_image']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $ext = strtolower(pathinfo($_FILES['product_image']['name'], PATHINFO_EXTENSION));
        if(in_array($ext, $allowed)) {
            $new_filename = time() . '_' . rand(1000, 9999) . '.' . $ext;
            $upload_path = '../uploads/requests/' . $new_filename;
            if(!is_dir('../uploads/requests')) mkdir('../uploads/requests', 0777, true);
            if(move_uploaded_file($_FILES['product_image']['tmp_name'], $upload_path)) {
                $image_path = 'uploads/requests/' . $new_filename;
            }
        }
    }
    
    $query = "INSERT INTO tblSellerRequests (SellerID, ProductName, Description, Brand, ImagePath, Status) 
              VALUES ($seller_id, '$product_name', '$description', '$brand', '$image_path', 'pending')";
    
    if(mysqli_query($conn, $query)) {
        $success = "Request submitted successfully! Admin will review your request.";
    } else {
        $error = "Error: " . mysqli_error($conn);
    }
}

include '../includes/header.php';
?>

<div style="max-width: 600px; margin: 20px auto; padding: 0 20px;">
    <h1>📦 Request to Sell</h1>
    <p>Submit your item for review by the administrator</p>
    
    <?php if($error): ?>
        <div class="alert alert-error">❌ <?php echo $error; ?></div>
    <?php endif; ?>
    
    <?php if($success): ?>
        <div class="alert alert-success">✅ <?php echo $success; ?></div>
    <?php endif; ?>
    
    <form method="POST" enctype="multipart/form-data" style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 3px 15px rgba(0,0,0,0.08);">
        <div class="form-group">
            <label>Product Name *</label>
            <input type="text" name="product_name" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
        </div>
        
        <div class="form-group">
            <label>Brand</label>
            <input type="text" name="brand" placeholder="e.g., Nike, Gucci" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
        </div>
        
        <div class="form-group">
            <label>Description *</label>
            <textarea name="description" rows="5" required placeholder="Describe your item in detail..." style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;"></textarea>
        </div>
        
        <div class="form-group">
            <label>Product Image</label>
            <input type="file" name="product_image" accept="image/*" style="width: 100%; padding: 10px;">
        </div>
        
        <button type="submit" class="btn btn-primary" style="width: 100%; padding: 15px;">Submit Request</button>
    </form>
</div>

<?php include '../includes/footer.php'; ?>