<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

require_once __DIR__ . '/../config/database.php';

$user = null;
if ($pdo) {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ? LIMIT 1");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();
}

// Fetch user orders
$user_orders = [];
$orders_file = __DIR__ . '/../data/orders.json';
if (file_exists($orders_file)) {
    $orders_data = json_decode(file_get_contents($orders_file), true);
    if (is_array($orders_data)) {
        foreach ($orders_data as $order) {
            if (isset($order['user_id']) && $order['user_id'] == $_SESSION['user_id']) {
                $user_orders[] = $order;
            }
        }
    }
}
$user_orders = array_reverse($user_orders); // newest first
$total_orders = count($user_orders);

$total_spent = 0;
foreach ($user_orders as $order) {
    if ($order['status'] !== 'Cancelled') {
        $total_spent += $order['total'];
    }
}

// Check if user is admin to show admin link
$admin_emails = ['admin@shopnoir.com', 'shubham@shopnoir.com'];
$is_admin = $user && in_array(strtolower($user['email']), array_map('strtolower', $admin_emails));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Account | ShopNoir</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/dashboard.css">
</head>
<body>

<nav class="navbar" id="navbar">
    <div class="nav-inner">
        <a href="index.php" class="nav-logo">Shop<span>Noir</span></a>

        <div class="nav-actions">
            <a href="index.php">Continue Shopping</a>
            <a href="logout.php">Sign Out</a>
        </div>
    </div>
</nav>

<div class="dashboard-main">

    <div class="dashboard-sidebar">
        <div class="user-greeting">
            <div class="user-avatar">
                <?php 
                $initials = '';
                if ($user) {
                    $initials = strtoupper(substr($user['first_name'], 0, 1) . substr($user['last_name'], 0, 1));
                } else {
                    $name_parts = explode(' ', $_SESSION['user_name']);
                    $initials = strtoupper(substr($name_parts[0], 0, 1) . (isset($name_parts[1]) ? substr($name_parts[1], 0, 1) : ''));
                }
                echo htmlspecialchars($initials);
                ?>
            </div>
            <h2><?php echo htmlspecialchars($user['first_name'] ?? $_SESSION['user_name']); ?></h2>
            <p><?php echo htmlspecialchars($user['email'] ?? $_SESSION['user_email']); ?></p>
        </div>

        <ul class="dashboard-menu">
            <li><a href="#" class="active">Overview</a></li>
            <li><a href="#orders">Order History (<?php echo $total_orders; ?>)</a></li>
            <li><a href="#profile">Profile Details</a></li>
            <?php if ($is_admin): ?>
            <li><a href="admin_dashboard.php" style="color: #6366f1;">Admin Panel</a></li>
            <?php endif; ?>
        </ul>
    </div>

    <div class="dashboard-content">
        <div class="welcome-header">
            <h1>My Account</h1>
            <p>Welcome back! Manage your orders and account details here.</p>
        </div>

        <?php if (!empty($db_error)) : ?>
            <div class="alert"><?php echo htmlspecialchars($db_error); ?></div>
        <?php endif; ?>

        <div class="stats-grid">
            <div class="stat-card">
                <p class="stat-label">Total Orders</p>
                <p class="stat-value"><?php echo $total_orders; ?></p>
            </div>
            <div class="stat-card">
                <p class="stat-label">Total Spent</p>
                <p class="stat-value">&#8377;<?php echo number_format($total_spent); ?></p>
            </div>
            <div class="stat-card">
                <p class="stat-label">Member Since</p>
                <p class="stat-value" style="font-size:1.2rem; margin-top:0.4rem; font-weight:600;"><?php echo $user ? date('M Y', strtotime($user['created_at'])) : '—'; ?></p>
            </div>
        </div>

        <div class="content-section" id="orders">
            <div class="section-header">
                <h2>Order History</h2>
                <a href="index.php" class="shop-link">Shop New Arrivals</a>
            </div>

            <?php if (empty($user_orders)) : ?>
                <div class="empty-state">
                    <p>You haven't placed any orders yet.</p>
                    <a href="index.php" class="btn-primary">Start Shopping</a>
                </div>
            <?php else : ?>
                <div class="orders-list">
                    <?php foreach ($user_orders as $order) : ?>
                    <div class="order-card">
                        <div class="order-header">
                            <div>
                                <p class="order-id">Order <?php echo htmlspecialchars($order['order_id']); ?></p>
                                <p class="order-date"><?php echo date('F j, Y', strtotime($order['date'])); ?></p>
                            </div>
                            <div class="order-status-wrap">
                                <span class="order-status"><?php echo htmlspecialchars($order['status']); ?></span>
                                <p class="order-total">&#8377;<?php echo number_format($order['total']); ?></p>
                            </div>
                        </div>
                        <div class="order-items">
                            <?php foreach ($order['items'] as $item) : ?>
                            <div class="order-item">
                                <img src="<?php echo htmlspecialchars($item['image']); ?>" alt="">
                                <div class="item-info">
                                    <p class="item-name"><?php echo htmlspecialchars($item['name']); ?></p>
                                    <p class="item-qty">Qty: <?php echo $item['qty']; ?> &nbsp;|&nbsp; &#8377;<?php echo number_format($item['price']); ?> each</p>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <?php if ($user) : ?>
        <div class="content-section" id="profile">
            <div class="section-header">
                <h2>Profile Details</h2>
            </div>
            <div class="profile-card">
                <table class="profile-table">
                    <tr>
                        <td>Full Name</td>
                        <td><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></td>
                    </tr>
                    <tr>
                        <td>Email Address</td>
                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                    </tr>
                    <tr>
                        <td>Password</td>
                        <td>••••••••</td>
                    </tr>
                </table>
            </div>
        </div>
        <?php endif; ?>

    </div>
</div>

<footer class="footer">
    <p>&copy; 2026 ShopNoir. All rights reserved.</p>
</footer>

<script>
    var navbar = document.getElementById('navbar');
    window.addEventListener('scroll', function() {
        if (window.scrollY > 10) {
            navbar.style.borderBottom = '1px solid #e0e0e0';
            navbar.style.boxShadow = '0 4px 20px rgba(0,0,0,0.03)';
        } else {
            navbar.style.borderBottom = '1px solid #111';
            navbar.style.boxShadow = 'none';
        }
    });

    // Smooth scrolling for sidebar links
    document.querySelectorAll('.dashboard-menu a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            document.querySelector(this.getAttribute('href')).scrollIntoView({
                behavior: 'smooth'
            });
            
            document.querySelectorAll('.dashboard-menu a').forEach(a => a.classList.remove('active'));
            this.classList.add('active');
        });
    });
</script>

</body>
</html>
