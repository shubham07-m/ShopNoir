<?php
require_once __DIR__ . '/../middleware/admin_auth.php';

$about_file = __DIR__ . '/../data/about_us.txt';
$success    = false;
$error      = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_content = $_POST['about_content'] ?? '';

    $written = file_put_contents($about_file, $new_content);
    if ($written !== false) {
        $_SESSION['admin_success'] = 'About Us content updated successfully!';
        header('Location: admin_about.php');
        exit;
    } else {
        $error = 'Failed to save. Check file permissions on data/about_us.txt.';
    }
}

$about_text = file_exists($about_file) ? file_get_contents($about_file) : '';

$success_msg = $_SESSION['admin_success'] ?? '';
unset($_SESSION['admin_success']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>Edit About Us | ShopNoir Admin</title>

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
        <li><a href="admin_add_product.php"><span class="nav-icon">+</span><span class="nav-label">Add Product</span></a></li>
        <li><a href="admin_about.php" class="active"><span class="nav-icon">&#9998;</span><span class="nav-label">About Us</span></a></li>
    </ul>
    <div class="sidebar-footer">
        <a href="index.php"><span class="nav-icon">&#8599;</span><span>View Store</span></a>
    </div>
</aside>

<main class="admin-main">
    <div class="admin-page-header">
        <h1>Edit About Us</h1>
        <p>Update the "About Us" section that appears on your homepage.</p>
    </div>

    <?php if ($success_msg) : ?>
        <div class="admin-alert admin-alert-success"><?php echo htmlspecialchars($success_msg); ?></div>
    <?php endif; ?>
    <?php if ($error) : ?>
        <div class="admin-alert admin-alert-error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <div class="admin-card">
        <h2>About Us Content</h2>

        <form method="POST" action="admin_about.php">
            <div class="admin-form-group">
                <label for="about-content">Content (plain text — will be displayed as paragraphs on the homepage)</label>
                <textarea id="about-content" name="about_content" rows="12" style="min-height:220px;"
                    placeholder="Write your store's About Us description here..."><?php echo htmlspecialchars($about_text); ?></textarea>
            </div>

            <div style="display:flex; align-items:center; gap:1rem;">
                <button type="submit" class="admin-btn admin-btn-primary">Save Changes</button>
                <a href="index.php#about" class="admin-btn admin-btn-outline" target="_blank">Preview on Homepage</a>
            </div>
        </form>
    </div>

    <div class="admin-card" style="margin-top:1rem;">
        <h2>Tips</h2>
        <ul style="list-style:none; display:flex; flex-direction:column; gap:0.6rem; color:#888; font-size:0.85rem;">
            <li>&#8226; Write naturally — the text will be split into paragraphs on the homepage</li>
            <li>&#8226; Keep it concise and engaging — aim for 3-5 sentences</li>
            <li>&#8226; Mention what makes your store unique</li>
            <li>&#8226; Changes appear instantly on the homepage after saving</li>
        </ul>
    </div>
</main>

<script src="js/admin.js"></script>
</body>
</html>
