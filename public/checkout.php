<?php
session_start();

$is_logged_in = isset($_SESSION['user_id']);
$user_name    = $_SESSION['user_name'] ?? '';
$user_email   = $_SESSION['user_email'] ?? '';

require_once __DIR__ . '/../config/products.php';

$total_price = 0;
$order_items = [];
if (isset($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $idx => $qty) {
        if (!isset($products[$idx])) continue;
        $line_total = $products[$idx]['price'] * $qty;
        $total_price += $line_total;
        $order_items[] = [
            'name'       => $products[$idx]['name'],
            'category'   => $products[$idx]['category'],
            'price'      => $products[$idx]['price'],
            'qty'        => $qty,
            'line_total' => $line_total,
            'image'      => $products[$idx]['image'],
        ];
    }
}
$shipping    = $total_price >= 999 ? 0 : ($total_price > 0 ? 99 : 0);
$grand_total = $total_price + $shipping;

$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $is_logged_in) {
    $order = [
        'order_id'   => 'ORD-' . strtoupper(substr(md5(uniqid()), 0, 8)),
        'user_id'    => $_SESSION['user_id'],
        'user_name'  => $user_name,
        'user_email' => $user_email,
        'items'      => $order_items,
        'subtotal'   => $total_price,
        'shipping'   => $shipping,
        'total'      => $grand_total,
        'status'     => 'Confirmed',
        'date'       => date('Y-m-d H:i:s'),
    ];

    $orders_file = __DIR__ . '/../data/orders.json';
    $orders = [];
    if (file_exists($orders_file)) {
        $orders = json_decode(file_get_contents($orders_file), true);
        if (!is_array($orders)) $orders = [];
    }
    $orders[] = $order;
    file_put_contents($orders_file, json_encode($orders, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

    $_SESSION['cart'] = [];
    $success = true;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout | ShopNoir</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/checkout.css">
    <style>
        /* Login required modal overlay */
        .login-modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.75);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            animation: modalFadeIn 0.3s ease;
        }

        @keyframes modalFadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes modalSlideUp {
            from { opacity: 0; transform: translateY(30px) scale(0.95); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }

        .login-modal {
            background: #fff;
            border: 1px solid #e0e0e0;
            border-radius: 16px;
            padding: 48px 40px;
            max-width: 420px;
            width: 90%;
            text-align: center;
            animation: modalSlideUp 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
            box-shadow: 0 25px 60px rgba(0, 0, 0, 0.15);
        }

        .login-modal .modal-icon {
            width: 72px;
            height: 72px;
            border-radius: 50%;
            background: #f5f5f5;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 24px;
            font-size: 1.8rem;
            border: 1px solid #111;
        }

        .login-modal .modal-icon svg {
            width: 32px;
            height: 32px;
            stroke: #111;
            fill: none;
            stroke-width: 2;
            stroke-linecap: round;
            stroke-linejoin: round;
        }

        .login-modal h2 {
            font-size: 1.5rem;
            font-weight: 700;
            color: #111;
            margin: 0 0 10px;
        }

        .login-modal p {
            color: #555;
            font-size: 0.92rem;
            line-height: 1.6;
            margin: 0 0 28px;
        }

        .login-modal .modal-btn-primary {
            display: inline-block;
            padding: 14px 36px;
            background: #111;
            color: #fff;
            border: 1px solid #111;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.95rem;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.2s;
            letter-spacing: 0.3px;
        }

        .login-modal .modal-btn-primary:hover {
            background: #fff;
            color: #111;
        }

        .login-modal .modal-btn-secondary {
            display: block;
            margin-top: 14px;
            color: #666;
            font-size: 0.85rem;
            text-decoration: none;
            transition: color 0.2s;
        }

        .login-modal .modal-btn-secondary:hover {
            color: #111;
        }

        .login-modal .redirect-text {
            display: block;
            margin-top: 20px;
            font-size: 0.78rem;
            color: #888;
        }

        .login-modal .redirect-text span {
            color: #111;
            font-weight: 600;
        }
    </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar">
    <div class="nav-inner">
        <a href="index.php" class="nav-logo">Shop<span>Noir</span></a>
        <a href="cart.php" class="back-link">&larr; Back to Cart</a>
    </div>
</nav>

<?php if (!$is_logged_in): ?>
<!-- Login Required Modal -->
<div class="login-modal-overlay" id="login-modal">
    <div class="login-modal">
        <div class="modal-icon">
            <svg viewBox="0 0 24 24">
                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
            </svg>
        </div>
        <h2>Sign In Required</h2>
        <p>You need to be signed in to complete your purchase. Your cart items will be saved.</p>
        <a href="login.php" class="modal-btn-primary">Sign In to Continue</a>
        <a href="cart.php" class="modal-btn-secondary">&larr; Back to Cart</a>
        <span class="redirect-text">Redirecting to sign in in <span id="countdown">5</span>s</span>
    </div>
</div>

<script>
    var count = 5;
    var el = document.getElementById('countdown');
    var timer = setInterval(function() {
        count--;
        if (el) el.textContent = count;
        if (count <= 0) {
            clearInterval(timer);
            window.location.href = 'login.php';
        }
    }, 1000);
</script>
<?php endif; ?>

<main class="checkout-page">
    <?php if ($success): ?>
        <div class="success-state">
            <div class="success-icon">&#10003;</div>
            <h1>Payment Successful!</h1>
            <p>Thank you for your order. Your transaction has been completed, and a receipt for your purchase has been emailed to you.</p>
            <a href="index.php" class="btn-primary">Return to Shop</a>
        </div>
    <?php elseif ($is_logged_in): ?>
        <div class="checkout-layout">
            <div class="checkout-form-section">
                <h2>Payment Details</h2>
                <p class="subtitle">Complete your purchase safely and securely.</p>

                <form action="checkout.php" method="POST" id="checkout-form">
                    <div class="form-group">
                        <label for="name">Name on Card</label>
                        <input type="text" id="name" name="name" required placeholder="Donald J Trump">
                    </div>

                    <div class="form-group">
                        <label for="card">Card Number</label>
                        <div class="card-input-wrapper">
                            <input type="text" id="card" name="card" required placeholder="0000 0000 0000 0000" maxlength="19">
                            <span class="card-icon">&#128179;</span>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group flex-1">
                            <label for="expiry">Expiry Date</label>
                            <input type="text" id="expiry" name="expiry" required placeholder="MM/YY" maxlength="5">
                        </div>
                        <div class="form-group flex-1">
                            <label for="cvc">CVC</label>
                            <input type="password" id="cvc" name="cvc" required placeholder="123" maxlength="3">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="address">Billing Address</label>
                        <input type="text" id="address" name="address" required placeholder="123 Luxury Ave, NY">
                    </div>

                    <button type="submit" class="btn-pay">Pay &#8377;<?php echo number_format($grand_total); ?> Now</button>
                    <p class="secure-note">Secured by 256-bit encryption</p>
                </form>
            </div>

            <div class="checkout-summary">
                <h3>Order Summary</h3>
                <div class="summary-line">
                    <span>Items Total</span>
                    <span>&#8377;<?php echo number_format($total_price); ?></span>
                </div>
                <div class="summary-line">
                    <span>Shipping</span>
                    <span><?php echo $shipping === 0 ? 'Free' : '&#8377;'.number_format($shipping); ?></span>
                </div>
                <div class="summary-total">
                    <span>Total</span>
                    <span>&#8377;<?php echo number_format($grand_total); ?></span>
                </div>
                <div class="trust-badges">
                    <p>&#10003; 30-Day Returns</p>
                    <p>&#10003; 24/7 Customer Support</p>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <?php if ($is_logged_in && !$success): ?>
    <!-- Mobile Sticky Summary -->
    <div class="mobile-sticky-summary" id="mobile-sticky-summary">
        <span>Order Total</span>
        <span>&#8377;<?php echo number_format($grand_total); ?></span>
    </div>
    <?php endif; ?>
</main>

<script>
    // Scroll logic for mobile sticky summary
    window.addEventListener('scroll', function() {
        if (window.innerWidth <= 900) {
            const summaryHero = document.querySelector('.checkout-summary');
            const stickySummary = document.getElementById('mobile-sticky-summary');
            const formSection = document.querySelector('.checkout-form-section');
            
            if (!summaryHero || !stickySummary || !formSection) return;
            
            const heroRect = summaryHero.getBoundingClientRect();
            const formRect = formSection.getBoundingClientRect();
            
            // Show pill when summary scrolls past navbar
            if (heroRect.bottom < 85 && formRect.bottom > 100) {
                stickySummary.classList.add('is-visible');
            } else {
                stickySummary.classList.remove('is-visible');
            }
        }
    });
    document.getElementById('card')?.addEventListener('input', function (e) {
        let value = e.target.value.replace(/\D/g, '').substring(0,16);
        value = value != '' ? value.match(/.{1,4}/g).join(' ') : '';
        e.target.value = value;
    });

    document.getElementById('expiry')?.addEventListener('input', function (e) {
        let value = e.target.value.replace(/\D/g, '').substring(0,4);
        if(value.length >= 3) {
            value = value.substring(0,2) + '/' + value.substring(2,4);
        }
        e.target.value = value;
    });
</script>
</body>
</html>
