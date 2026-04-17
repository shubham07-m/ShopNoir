<?php
require_once __DIR__ . '/../middleware/admin_auth.php';

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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>Orders | ShopNoir Admin</title>

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
        <li><a href="admin_orders.php" class="active"><span class="nav-icon">&#9776;</span><span class="nav-label">Orders</span></a></li>
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
            <h1>Orders</h1>
            <p><?php echo $total_orders; ?> order<?php echo $total_orders !== 1 ? 's' : ''; ?> · &#8377;<?php echo number_format($total_revenue); ?> total revenue</p>
        </div>
    </div>

    <div class="admin-card" style="padding:1rem;">
        <?php if (empty($orders)) : ?>
            <div style="text-align:center; padding:3rem; color:#666;">
                <h3 style="color:#888; font-size:1rem; margin-bottom:0.5rem;">No Orders Yet</h3>
                <p style="font-size:0.85rem;">When customers complete checkout, their orders will appear here.</p>
            </div>
        <?php else : ?>
            <div class="admin-table-wrap">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Customer</th>
                            <th>Items</th>
                            <th>Subtotal</th>
                            <th>Shipping</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $o) : ?>
                        <tr>
                            <td style="color:#818cf8; font-weight:600;"><?php echo htmlspecialchars($o['order_id']); ?></td>
                            <td>
                                <div style="font-weight:500; color:#fff;"><?php echo htmlspecialchars($o['user_name']); ?></div>
                                <div style="font-size:0.72rem; color:#555;"><?php echo htmlspecialchars($o['user_email']); ?></div>
                            </td>
                            <td>
                                <?php foreach ($o['items'] as $item) : ?>
                                    <div style="font-size:0.8rem; color:#aaa; margin-bottom:2px;">
                                        <?php echo htmlspecialchars($item['name']); ?> &times; <?php echo $item['qty']; ?>
                                    </div>
                                <?php endforeach; ?>
                            </td>
                            <td>&#8377;<?php echo number_format($o['subtotal']); ?></td>
                            <td><?php echo $o['shipping'] === 0 ? '<span style="color:#22c55e;">Free</span>' : '&#8377;' . number_format($o['shipping']); ?></td>
                            <td style="font-weight:600; color:#22c55e;">&#8377;<?php echo number_format($o['total']); ?></td>
                            <td><span class="table-badge table-badge-new"><?php echo htmlspecialchars($o['status']); ?></span></td>
                            <td style="color:#888; font-size:0.82rem;"><?php echo date('M j, Y', strtotime($o['date'])); ?><br><span style="color:#555;"><?php echo date('g:i A', strtotime($o['date'])); ?></span></td>
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
