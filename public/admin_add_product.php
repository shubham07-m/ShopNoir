<?php
require_once __DIR__ . '/../middleware/admin_auth.php';

$success = false;
$error   = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name        = trim($_POST['name'] ?? '');
    $category    = trim($_POST['category'] ?? '');
    $price       = intval($_POST['price'] ?? 0);
    $old_price   = trim($_POST['old_price'] ?? '');
    $old_price   = $old_price !== '' ? intval($old_price) : null;
    $badge       = trim($_POST['badge'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $image       = trim($_POST['image'] ?? '');

    if ($name === '' || $category === '' || $price <= 0) {
        $error = 'Please fill in product name, category, and a valid price.';
    } else {
        if ($image === '') {
            $image = 'images/products/placeholder.png';
        }

        $new_product = [
            'name'        => $name,
            'category'    => $category,
            'price'       => $price,
            'old_price'   => $old_price,
            'image'       => $image,
            'badge'       => $badge,
            'description' => $description,
            'photos'      => [$image],
            'reviews'     => [],
        ];

        $json_path = __DIR__ . '/../data/products.json';
        $products = [];
        if (file_exists($json_path)) {
            $products = json_decode(file_get_contents($json_path), true);
            if (!is_array($products)) $products = [];
        }

        $products[] = $new_product;
        $written = file_put_contents($json_path, json_encode($products, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        if ($written !== false) {
            $_SESSION['admin_success'] = 'Product "' . $name . '" added successfully!';
            header('Location: admin_manage_products.php');
            exit;
        } else {
            $error = 'Failed to write to products file. Check file permissions.';
        }
    }
}

$categories = ['Accessories', 'Watches', 'Footwear', 'Eyewear', 'Outerwear', 'Fragrance', 'Audio', 'Electronics', 'Clothing'];
$badges     = ['', 'Sale', 'New', 'Trending', 'Best Seller', 'Limited'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>Add Product | ShopNoir Admin</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/admin.css">
</head>
<body class="admin-body">

<aside class="admin-sidebar">
    <div class="sidebar-logo">
        <a href="admin_dashboard.php">Shop<span>Noir</span></a>
        <span class="sidebar-badge">Admin</span>
    </div>
    <ul class="sidebar-nav">
        <li><a href="admin_dashboard.php"><span class="nav-icon">&#9636;</span><span class="nav-label">Dashboard</span></a></li>
        <li><a href="admin_manage_products.php"><span class="nav-icon">&#9744;</span><span class="nav-label">Products</span></a></li>
        <li><a href="admin_add_product.php" class="active"><span class="nav-icon">+</span><span class="nav-label">Add Product</span></a></li>
        <li><a href="admin_about.php"><span class="nav-icon">&#9998;</span><span class="nav-label">About Us</span></a></li>
    </ul>
    <div class="sidebar-footer">
        <a href="index.php"><span class="nav-icon">&#8599;</span><span>View Store</span></a>
    </div>
</aside>

<main class="admin-main">
    <div class="admin-page-header">
        <h1>Add New Product</h1>
        <p>Fill in the details below to add a product to your catalogue.</p>
    </div>

    <?php if ($error) : ?>
        <div class="admin-alert admin-alert-error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <div class="admin-card">
        <h2>Product Details</h2>

        <form method="POST" action="admin_add_product.php">

            <div class="admin-form-group">
                <label for="product-name">Product Name *</label>
                <input type="text" id="product-name" name="name" placeholder="e.g. Classic Leather Bag" required
                       value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>">
            </div>

            <div class="admin-form-row">
                <div class="admin-form-group">
                    <label for="product-category">Category *</label>
                    <select id="product-category" name="category" required>
                        <option value="">— Select Category —</option>
                        <?php foreach ($categories as $cat) : ?>
                            <option value="<?php echo $cat; ?>" <?php echo (($_POST['category'] ?? '') === $cat) ? 'selected' : ''; ?>>
                                <?php echo $cat; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="admin-form-group">
                    <label for="product-badge">Badge / Tag</label>
                    <select id="product-badge" name="badge">
                        <?php foreach ($badges as $b) : ?>
                            <option value="<?php echo $b; ?>" <?php echo (($_POST['badge'] ?? '') === $b) ? 'selected' : ''; ?>>
                                <?php echo $b === '' ? '— None —' : $b; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="admin-form-row">
                <div class="admin-form-group">
                    <label for="product-price">Price (&#8377;) *</label>
                    <input type="number" id="product-price" name="price" placeholder="e.g. 15999" min="1" required
                           value="<?php echo htmlspecialchars($_POST['price'] ?? ''); ?>">
                </div>
                <div class="admin-form-group">
                    <label for="product-old-price">Old Price (&#8377;) — optional</label>
                    <input type="number" id="product-old-price" name="old_price" placeholder="e.g. 20999" min="0"
                           value="<?php echo htmlspecialchars($_POST['old_price'] ?? ''); ?>">
                </div>
            </div>

            <div class="admin-form-group">
                <label for="product-image-url">Image Path / URL</label>
                <input type="text" id="product-image-url" name="image" placeholder="images/products/your-image.png"
                       value="<?php echo htmlspecialchars($_POST['image'] ?? ''); ?>">
                <img id="image-preview" src="" alt="Preview" style="display:none; margin-top:0.75rem; max-width:180px; border-radius:10px; border:1px solid rgba(255,255,255,0.1);">
            </div>

            <div class="admin-form-group">
                <label for="product-description">Description</label>
                <textarea id="product-description" name="description" placeholder="Describe the product in detail..."><?php echo htmlspecialchars($_POST['description'] ?? ''); ?></textarea>
            </div>

            <button type="submit" class="admin-btn admin-btn-primary">Add Product</button>
        </form>
    </div>
</main>

<script src="js/admin.js"></script>
</body>
</html>
