<?php
// admin/admin_dashboard.php - Admin dashboard with image management
include '../config/DBConn.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'admin') {
    header("Location: admin_login.php");
    exit();
}

// Handle Add
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_item'])) {
    $product_name = mysqli_real_escape_string($conn, $_POST['product_name']);
    $category = mysqli_real_escape_string($conn, $_POST['category']);
    $price = floatval($_POST['price']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $brand = mysqli_real_escape_string($conn, $_POST['brand']);
    $size = mysqli_real_escape_string($conn, $_POST['size']);
    $condition = mysqli_real_escape_string($conn, $_POST['condition']);
    $quantity = intval($_POST['quantity']);
    
    // Handle image upload in admin
    $image_path = '';
    if(isset($_FILES['product_image']) && $_FILES['product_image']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $ext = strtolower(pathinfo($_FILES['product_image']['name'], PATHINFO_EXTENSION));
        if(in_array($ext, $allowed)) {
            if(!is_dir('../uploads/products')) mkdir('../uploads/products', 0777, true);
            $new_filename = time() . '_' . rand(1000, 9999) . '.' . $ext;
            $upload_path = '../uploads/products/' . $new_filename;
            if(move_uploaded_file($_FILES['product_image']['tmp_name'], $upload_path)) {
                $image_path = 'uploads/products/' . $new_filename;
            }
        }
    }
    
    $query = "INSERT INTO tblproducts (SellerID, ProductName, CategoryID, Price, Description, Brand, Size, Condition, Quantity, ImagePath, Status) 
              VALUES (1, '$product_name', 1, $price, '$description', '$brand', '$size', '$condition', $quantity, '$image_path', 'approved')";
    mysqli_query($conn, $query);
    header("Location: admin_dashboard.php?msg=Item added");
    exit();
}

// Handle Delete
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    mysqli_query($conn, "DELETE FROM tblproducts WHERE ProductID = $id");
    header("Location: admin_dashboard.php?msg=Item deleted");
    exit();
}

// Handle Edit
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['edit_item'])) {
    $id = intval($_POST['product_id']);
    $product_name = mysqli_real_escape_string($conn, $_POST['product_name']);
    $category = mysqli_real_escape_string($conn, $_POST['category']);
    $price = floatval($_POST['price']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $brand = mysqli_real_escape_string($conn, $_POST['brand']);
    $size = mysqli_real_escape_string($conn, $_POST['size']);
    $condition = mysqli_real_escape_string($conn, $_POST['condition']);
    $quantity = intval($_POST['quantity']);
    
    $query = "UPDATE tblproducts SET 
              ProductName = '$product_name',
              CategoryID = '$category',
              Price = $price,
              Description = '$description',
              Brand = '$brand',
              Size = '$size',
              Condition = '$condition',
              Quantity = $quantity
              WHERE ProductID = $id";
    mysqli_query($conn, $query);
    header("Location: admin_dashboard.php?msg=Item updated");
    exit();
}

$items = mysqli_query($conn, "SELECT p.*, c.CategoryName FROM tblproducts p JOIN tblcategories c ON p.CategoryID = c.CategoryID ORDER BY p.ProductID DESC");
$message = isset($_GET['msg']) ? $_GET['msg'] : '';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard - Pastimes</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .admin-container { display: flex; }
        .admin-sidebar { width: 250px; background: #1a1a2e; color: white; padding: 20px; min-height: 100vh; }
        .admin-sidebar a { color: rgba(255,255,255,0.8); text-decoration: none; display: block; padding: 12px 15px; border-radius: 8px; margin: 5px 0; }
        .admin-sidebar a:hover { background: rgba(255,255,255,0.1); }
        .admin-sidebar a.active { background: #e74c3c; }
        .admin-content { flex: 1; padding: 30px; background: #f0f2f5; }
        .alert-success { background: #d4edda; color: #155724; padding: 15px; border-radius: 8px; margin-bottom: 20px; }
        .modal { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; }
        .modal-content { background: white; max-width: 600px; margin: 50px auto; padding: 30px; border-radius: 10px; }
        .admin-thumbnail { width: 50px; height: 50px; object-fit: cover; border-radius: 5px; }
    </style>
</head>
<body>
<div class="admin-container">
    <div class="admin-sidebar">
        <h3>👑 Admin Panel</h3>
        <hr style="border-color: rgba(255,255,255,0.1);">
        <a href="admin_dashboard.php" class="active">📊 Dashboard</a>
        <a href="admin_users.php">👥 Users</a>
        <a href="admin_communication.php">💬 Communications</a>
        <a href="../logout.php">🚪 Logout</a>
    </div>
    
    <div class="admin-content">
        <h1>📊 Admin Dashboard</h1>
        
        <?php if($message): ?>
            <div class="alert-success">✅ <?php echo $message; ?></div>
        <?php endif; ?>
        
        <div style="margin-bottom: 20px;">
            <button onclick="openAddModal()" class="btn btn-primary">➕ Add New Item</button>
            <a href="../shop.php" class="btn btn-secondary">🛍️ View Shop</a>
        </div>
        
        <h2>Clothing Items</h2>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Image</th>
                    <th>Name</th>
                    <th>Category</th>
                    <th>Brand</th>
                    <th>Price</th>
                    <th>Stock</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while($item = mysqli_fetch_assoc($items)): 
                    $imagePath = !empty($item['ImagePath']) ? '../' . $item['ImagePath'] : '../images/placeholders/product.png';
                ?>
                <tr>
                    <td><?php echo $item['ProductID']; ?></td>
                    <td>
                        <img src="<?php echo $imagePath; ?>" 
                             class="admin-thumbnail"
                             onerror="this.src='../images/placeholders/no-image.png'">
                    </td>
                    <td><?php echo htmlspecialchars($item['ProductName']); ?></td>
                    <td><?php echo htmlspecialchars($item['CategoryName']); ?></td>
                    <td><?php echo htmlspecialchars($item['Brand'] ?: '-'); ?></td>
                    <td>R<?php echo number_format($item['Price'], 2); ?></td>
                    <td><?php echo $item['Quantity']; ?></td>
                    <td>
                        <button onclick="openEditModal(
                            <?php echo $item['ProductID']; ?>,
                            '<?php echo addslashes($item['ProductName']); ?>',
                            <?php echo $item['CategoryID']; ?>,
                            <?php echo $item['Price']; ?>,
                            '<?php echo addslashes($item['Description']); ?>',
                            '<?php echo addslashes($item['Brand']); ?>',
                            '<?php echo addslashes($item['Size']); ?>',
                            '<?php echo addslashes($item['Condition']); ?>',
                            <?php echo $item['Quantity']; ?>
                        )" class="btn-sm btn-edit">✏️ Edit</button>
                        <a href="?delete=<?php echo $item['ProductID']; ?>" class="btn-sm btn-delete" 
                           onclick="return confirm('Delete this item?')">🗑 Delete</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Add Modal -->
<div id="addModal" class="modal">
    <div class="modal-content">
        <h2>➕ Add New Item</h2>
        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label>Product Name *</label>
                <input type="text" name="product_name" required>
            </div>
            <div class="form-group">
                <label>Category *</label>
                <input type="text" name="category" required>
            </div>
            <div class="form-group">
                <label>Price (R) *</label>
                <input type="number" step="0.01" name="price" required>
            </div>
            <div class="form-group">
                <label>Description</label>
                <textarea name="description" rows="3"></textarea>
            </div>
            <div class="form-group">
                <label>Brand</label>
                <input type="text" name="brand">
            </div>
            <div class="form-group">
                <label>Size</label>
                <input type="text" name="size" placeholder="S, M, L, 32x32, etc.">
            </div>
            <div class="form-group">
                <label>Condition *</label>
                <select name="condition">
                    <option value="new">New</option>
                    <option value="like-new">Like New</option>
                    <option value="good">Good</option>
                    <option value="fair">Fair</option>
                </select>
            </div>
            <div class="form-group">
                <label>Quantity *</label>
                <input type="number" name="quantity" value="1" required>
            </div>
            <div class="form-group">
                <label>Product Image</label>
                <input type="file" name="product_image" accept="image/*">
            </div>
            <button type="submit" name="add_item" class="btn btn-primary">Add Item</button>
            <button type="button" onclick="closeModal('addModal')" class="btn btn-secondary">Cancel</button>
        </form>
    </div>
</div>

<!-- Edit Modal -->
<div id="editModal" class="modal">
    <div class="modal-content">
        <h2>✏️ Edit Item</h2>
        <form method="POST">
            <input type="hidden" name="product_id" id="edit_product_id">
            <div class="form-group">
                <label>Product Name *</label>
                <input type="text" name="product_name" id="edit_product_name" required>
            </div>
            <div class="form-group">
                <label>Category ID *</label>
                <input type="number" name="category" id="edit_category" required>
            </div>
            <div class="form-group">
                <label>Price (R) *</label>
                <input type="number" step="0.01" name="price" id="edit_price" required>
            </div>
            <div class="form-group">
                <label>Description</label>
                <textarea name="description" id="edit_description" rows="3"></textarea>
            </div>
            <div class="form-group">
                <label>Brand</label>
                <input type="text" name="brand" id="edit_brand">
            </div>
            <div class="form-group">
                <label>Size</label>
                <input type="text" name="size" id="edit_size">
            </div>
            <div class="form-group">
                <label>Condition *</label>
                <select name="condition" id="edit_condition">
                    <option value="new">New</option>
                    <option value="like-new">Like New</option>
                    <option value="good">Good</option>
                    <option value="fair">Fair</option>
                </select>
            </div>
            <div class="form-group">
                <label>Quantity *</label>
                <input type="number" name="quantity" id="edit_quantity" required>
            </div>
            <button type="submit" name="edit_item" class="btn btn-primary">Update Item</button>
            <button type="button" onclick="closeModal('editModal')" class="btn btn-secondary">Cancel</button>
        </form>
    </div>
</div>

<script>
function openAddModal() {
    document.getElementById('addModal').style.display = 'block';
}

function openEditModal(id, name, category, price, description, brand, size, condition, quantity) {
    document.getElementById('edit_product_id').value = id;
    document.getElementById('edit_product_name').value = name;
    document.getElementById('edit_category').value = category;
    document.getElementById('edit_price').value = price;
    document.getElementById('edit_description').value = description || '';
    document.getElementById('edit_brand').value = brand || '';
    document.getElementById('edit_size').value = size || '';
    document.getElementById('edit_condition').value = condition || 'good';
    document.getElementById('edit_quantity').value = quantity;
    document.getElementById('editModal').style.display = 'block';
}

function closeModal(id) {
    document.getElementById(id).style.display = 'none';
}

window.onclick = function(event) {
    if (event.target.classList.contains('modal')) {
        event.target.style.display = 'none';
    }
}
</script>
</body>
</html>