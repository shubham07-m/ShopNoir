<?php
require_once __DIR__ . '/../middleware/admin_auth.php';

require_once __DIR__ . '/../config/products.php';

$success_msg = $_SESSION['admin_success'] ?? '';
$error_msg   = $_SESSION['admin_error'] ?? '';
unset($_SESSION['admin_success'], $_SESSION['admin_error']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>Manage Products | ShopNoir Admin</title>

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
        <li><a href="admin_manage_products.php" class="active"><span class="nav-icon">&#9744;</span><span class="nav-label">Products</span></a></li>
        <li><a href="admin_add_product.php"><span class="nav-icon">+</span><span class="nav-label">Add Product</span></a></li>
        <li><a href="admin_about.php"><span class="nav-icon">&#9998;</span><span class="nav-label">About Us</span></a></li>
    </ul>
    <div class="sidebar-footer">
        <a href="index.php"><span class="nav-icon">&#8599;</span><span>View Store</span></a>
    </div>
</aside>

<main class="admin-main">
    <div class="admin-page-header" style="display:flex; justify-content:space-between; align-items:flex-start; flex-wrap:wrap; gap:1rem;">
        <div>
            <h1>Manage Products</h1>
            <p><?php echo count($products); ?> product<?php echo count($products) !== 1 ? 's' : ''; ?> in catalogue</p>
        </div>
        <a href="admin_add_product.php" class="admin-btn admin-btn-primary">+ Add New</a>
    </div>

    <?php if ($success_msg) : ?>
        <div class="admin-alert admin-alert-success"><?php echo htmlspecialchars($success_msg); ?></div>
    <?php endif; ?>
    <?php if ($error_msg) : ?>
        <div class="admin-alert admin-alert-error"><?php echo htmlspecialchars($error_msg); ?></div>
    <?php endif; ?>

    <div class="admin-card" style="padding:1rem;">
        <?php if (empty($products)) : ?>
            <div style="text-align:center; padding:3rem; color:#666;">
                <h3 style="color:#888; font-size:1rem; margin-bottom:0.5rem;">No Products Yet</h3>
                <p style="font-size:0.85rem;">Your catalogue is empty. Start by adding your first product!</p>
                <a href="admin_add_product.php" class="admin-btn admin-btn-primary" style="margin-top:1rem;">+ Add Product</a>
            </div>
        <?php else : ?>
            <div class="admin-table-wrap">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Image</th>
                            <th>Name</th>
                            <th>Category</th>
                            <th>Price</th>
                            <th>Old Price</th>
                            <th>Badge</th>
                            <th>Reviews</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($products as $idx => $p) :
                            $badge_class = '';
                            $b = strtolower($p['badge'] ?? '');
                            if ($b === 'sale') $badge_class = 'table-badge-sale';
                            elseif ($b === 'new') $badge_class = 'table-badge-new';
                            elseif ($b === 'trending') $badge_class = 'table-badge-trending';
                            elseif ($b === 'best seller') $badge_class = 'table-badge-bestseller';
                        ?>
                        <tr>
                            <td style="color:#555;"><?php echo $idx + 1; ?></td>
                            <td><img class="table-product-img" src="<?php echo htmlspecialchars($p['image']); ?>" alt=""></td>
                            <td class="table-product-name"><?php echo htmlspecialchars($p['name']); ?></td>
                            <td><?php echo htmlspecialchars($p['category']); ?></td>
                            <td>&#8377;<?php echo number_format($p['price']); ?></td>
                            <td>
                                <?php if (!empty($p['old_price'])) : ?>
                                    <span style="color:#888; text-decoration:line-through;">&#8377;<?php echo number_format($p['old_price']); ?></span>
                                <?php else : ?>
                                    <span style="color:#555;">—</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if (!empty($p['badge'])) : ?>
                                    <span class="table-badge <?php echo $badge_class; ?>"><?php echo htmlspecialchars($p['badge']); ?></span>
                                <?php else : ?>
                                    <span style="color:#555;">—</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo count($p['reviews'] ?? []); ?></td>
                            <td>
                                <div class="table-actions">
                                    <a href="product.php?id=<?php echo $idx; ?>" class="table-action-btn" title="View on store" target="_blank">View</a>
                                    <a href="admin_delete_product.php?id=<?php echo $idx; ?>"
                                       class="table-action-btn delete-btn delete-product-btn"
                                       data-product-name="<?php echo htmlspecialchars($p['name']); ?>"
                                       title="Delete product">Delete</a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</main>

<script src="js/admin.js"></script>
</body>
</html>
