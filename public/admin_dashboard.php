<?php
require_once __DIR__ . '/../middleware/admin_auth.php';

require_once __DIR__ . '/../config/products.php';

$orders_file = __DIR__ . '/../data/orders.json';
$orders = [];
if (file_exists($orders_file)) {
    $orders = json_decode(file_get_contents($orders_file), true);
    if (!is_array($orders)) $orders = [];
}
$orders = array_reverse($orders);
$total_orders = count($orders);
$total_revenue = 0;
foreach ($orders as $o) {
    $total_revenue += $o['total'] ?? 0;
}

$about_file = __DIR__ . '/../data/about_us.txt';
$about_text = file_exists($about_file) ? file_get_contents($about_file) : '';

$total_products = count($products);
$total_categories = count(array_unique(array_column($products, 'category')));
$total_reviews = 0;
$total_revenue_potential = 0;
foreach ($products as $p) {
    $total_reviews += count($p['reviews'] ?? []);
    $total_revenue_potential += $p['price'];
}
$avg_price = $total_products > 0 ? round($total_revenue_potential / $total_products) : 0;

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
    <title>Admin Dashboard | ShopNoir</title>

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
        <li><a href="admin_dashboard.php" class="active"><span class="nav-icon">&#9636;</span><span class="nav-label">Dashboard</span></a></li>
        <li><a href="admin_manage_products.php"><span class="nav-icon">&#9744;</span><span class="nav-label">Products</span></a></li>
        <li><a href="admin_orders.php"><span class="nav-icon">&#9776;</span><span class="nav-label">Orders</span></a></li>
        <li><a href="admin_add_product.php"><span class="nav-icon">+</span><span class="nav-label">Add Product</span></a></li>
        <li><a href="admin_about.php"><span class="nav-icon">&#9998;</span><span class="nav-label">About Us</span></a></li>
    </ul>

    <div class="sidebar-footer">
        <a href="index.php"><span class="nav-icon">&#8599;</span><span>View Store</span></a>
    </div>
</aside>

<main class="admin-main">

    <div class="admin-page-header">
        <h1>Dashboard</h1>
        <p>Welcome back — here's what's happening with your store.</p>
    </div>

    <?php if ($success_msg) : ?>
        <div class="admin-alert admin-alert-success"><?php echo htmlspecialchars($success_msg); ?></div>
    <?php endif; ?>
    <?php if ($error_msg) : ?>
        <div class="admin-alert admin-alert-error"><?php echo htmlspecialchars($error_msg); ?></div>
    <?php endif; ?>

    <div class="admin-stats-grid">
        <div class="admin-stat-card">
            <div class="stat-card-icon">&#9744;</div>
            <div class="stat-card-value"><?php echo $total_products; ?></div>
            <div class="stat-card-label">Total Products</div>
        </div>
        <div class="admin-stat-card">
            <div class="stat-card-icon">&#9776;</div>
            <div class="stat-card-value"><?php echo $total_orders; ?></div>
            <div class="stat-card-label">Total Orders</div>
        </div>
        <div class="admin-stat-card">
            <div class="stat-card-icon">&#8377;</div>
            <div class="stat-card-value">&#8377;<?php echo number_format($total_revenue); ?></div>
            <div class="stat-card-label">Revenue</div>
        </div>
        <div class="admin-stat-card">
            <div class="stat-card-icon">&#9679;</div>
            <div class="stat-card-value"><?php echo $total_categories; ?></div>
            <div class="stat-card-label">Categories</div>
        </div>
        <div class="admin-stat-card">
            <div class="stat-card-icon">&#9733;</div>
            <div class="stat-card-value"><?php echo $total_reviews; ?></div>
            <div class="stat-card-label">Reviews</div>
        </div>
    </div>

    <div class="admin-card">
        <h2>Quick Actions</h2>
        <div class="quick-actions">
            <a href="admin_add_product.php" class="quick-action-card">
                <div class="quick-action-icon">+</div>
                <div class="quick-action-title">Add Product</div>
                <div class="quick-action-desc">Create a new listing</div>
            </a>
            <a href="admin_manage_products.php" class="quick-action-card">
                <div class="quick-action-icon">&#9776;</div>
                <div class="quick-action-title">Manage Products</div>
                <div class="quick-action-desc">Edit or remove items</div>
            </a>
            <a href="admin_about.php" class="quick-action-card">
                <div class="quick-action-icon">&#9998;</div>
                <div class="quick-action-title">Edit About Us</div>
                <div class="quick-action-desc">Update store description</div>
            </a>
            <a href="index.php" class="quick-action-card" target="_blank">
                <div class="quick-action-icon">&#8599;</div>
                <div class="quick-action-title">View Store</div>
                <div class="quick-action-desc">See the live site</div>
            </a>
        </div>
    </div>

    <div class="admin-card">
        <h2>Recent Products</h2>
        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Name</th>
                        <th>Category</th>
                        <th>Price</th>
                        <th>Badge</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $recents = array_slice($products, -5);
                    $recents = array_reverse($recents);
                    foreach ($recents as $p) :
                        $badge_class = '';
                        $b = strtolower($p['badge'] ?? '');
                        if ($b === 'sale') $badge_class = 'table-badge-sale';
                        elseif ($b === 'new') $badge_class = 'table-badge-new';
                        elseif ($b === 'trending') $badge_class = 'table-badge-trending';
                        elseif ($b === 'best seller') $badge_class = 'table-badge-bestseller';
                    ?>
                    <tr>
                        <td><img class="table-product-img" src="<?php echo htmlspecialchars($p['image']); ?>" alt=""></td>
                        <td class="table-product-name"><?php echo htmlspecialchars($p['name']); ?></td>
                        <td><?php echo htmlspecialchars($p['category']); ?></td>
                        <td>&#8377;<?php echo number_format($p['price']); ?></td>
                        <td>
                            <?php if (!empty($p['badge'])) : ?>
                                <span class="table-badge <?php echo $badge_class; ?>"><?php echo htmlspecialchars($p['badge']); ?></span>
                            <?php else : ?>
                                <span style="color: #555;">—</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="admin-card">
        <h2>Recent Orders</h2>
        <?php if (empty($orders)) : ?>
            <p style="color:#555; font-size:0.88rem; padding:1rem 0;">No orders yet. Orders will appear here when customers check out.</p>
        <?php else : ?>
        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Customer</th>
                        <th>Items</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach (array_slice($orders, 0, 10) as $o) : ?>
                    <tr>
                        <td style="color:#818cf8; font-weight:600;"><?php echo htmlspecialchars($o['order_id']); ?></td>
                        <td>
                            <div style="font-weight:500; color:#fff;"><?php echo htmlspecialchars($o['user_name']); ?></div>
                            <div style="font-size:0.75rem; color:#555;"><?php echo htmlspecialchars($o['user_email']); ?></div>
                        </td>
                        <td><?php echo count($o['items']); ?> item<?php echo count($o['items']) !== 1 ? 's' : ''; ?></td>
                        <td style="font-weight:600; color:#22c55e;">&#8377;<?php echo number_format($o['total']); ?></td>
                        <td><span class="table-badge table-badge-new"><?php echo htmlspecialchars($o['status']); ?></span></td>
                        <td style="color:#888;"><?php echo date('M j, Y g:i A', strtotime($o['date'])); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php if ($total_orders > 10) : ?>
            <a href="admin_orders.php" style="display:inline-block; margin-top:1rem; color:#818cf8; font-size:0.85rem; font-weight:500;">View all <?php echo $total_orders; ?> orders &rarr;</a>
        <?php endif; ?>
        <?php endif; ?>
    </div>

</main>

<script src="js/admin.js"></script>
</body>
</html>
