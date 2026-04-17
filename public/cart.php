<?php
session_start();

$is_logged_in = isset($_SESSION['user_id']);
$user_name    = $_SESSION['user_name'] ?? '';

require_once __DIR__ . '/../config/products.php';

$cart_items  = [];
$total_items = 0;
$total_price = 0;

if (isset($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $idx => $qty) {
        if (!isset($products[$idx])) continue;
        $product    = $products[$idx];
        $line_total = $product['price'] * $qty;
        $total_items += $qty;
        $total_price += $line_total;

        $cart_items[] = [
            'index'      => $idx,
            'name'       => $product['name'],
            'category'   => $product['category'],
            'price'      => $product['price'],
            'old_price'  => $product['old_price'],
            'image'      => $product['image'],
            'qty'        => $qty,
            'line_total' => $line_total,
        ];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Your shopping cart at ShopNoir — review your selected items and proceed to checkout.">
    <title>Your Cart | ShopNoir</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/cart.css">
</head>
<body>

<nav class="navbar" id="navbar">
    <div class="nav-inner">
        <a href="index.php" class="nav-logo">Shop<span>Noir</span></a>

        <ul class="nav-links">
            <li><a href="index.php#categories">Categories</a></li>
            <li><a href="index.php#products">Shop</a></li>
            <li><a href="index.php#promo">Deals</a></li>
            <li><a href="index.php#footer">Contact</a></li>
        </ul>

        <div class="nav-actions">
            <?php if ($is_logged_in) : ?>
                <a href="dashboard.php"><?php echo htmlspecialchars($user_name); ?></a>
                <a href="logout.php">Logout</a>
            <?php else : ?>
                <a href="login.php">Sign In</a>
                <a href="signup.php">Sign Up</a>
            <?php endif; ?>
            <a href="cart.php" class="cart-icon" id="cart-link">&#128722;<span class="cart-badge" id="cart-badge" style="<?php echo $total_items === 0 ? 'display:none;' : ''; ?>"><?php echo $total_items; ?></span></a>
        </div>
    </div>
</nav>

<main class="cart-page">
    <div class="cart-breadcrumb">
        <a href="index.php">Home</a>
        <span class="breadcrumb-sep">&#8250;</span>
        <span>Your Cart</span>
    </div>

    <div class="cart-page-header">
        <h1>Your Cart</h1>
        <p id="cart-item-count"><?php echo $total_items; ?> item<?php echo $total_items !== 1 ? 's' : ''; ?></p>
    </div>

    <?php if (empty($cart_items)) : ?>
    <div class="cart-empty-state" id="cart-empty-state">
        <div class="cart-empty-icon">&#128722;</div>
        <h2>Your cart is empty</h2>
        <p>Looks like you haven't added anything yet. Browse our collection and find something you love!</p>
        <a href="index.php#products" class="btn-continue-shopping">Continue Shopping</a>
    </div>

    <?php else : ?>
    <div class="cart-layout" id="cart-layout">
        <div class="cart-items-section" id="cart-items-section">
            <?php foreach ($cart_items as $item) : ?>
            <div class="cart-row" data-index="<?php echo $item['index']; ?>" id="cart-row-<?php echo $item['index']; ?>">
                <div class="cart-row-img">
                    <img src="<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>">
                </div>
                <div class="cart-row-details">
                    <p class="cart-row-category"><?php echo htmlspecialchars($item['category']); ?></p>
                    <h3 class="cart-row-name"><?php echo htmlspecialchars($item['name']); ?></h3>
                    <p class="cart-row-unit-price">&#8377;<?php echo number_format($item['price']); ?> each</p>
                    <div class="cart-row-actions">
                        <div class="qty-controls">
                            <button class="qty-btn qty-minus" data-index="<?php echo $item['index']; ?>">&#8722;</button>
                            <span class="qty-value" id="qty-<?php echo $item['index']; ?>"><?php echo $item['qty']; ?></span>
                            <button class="qty-btn qty-plus" data-index="<?php echo $item['index']; ?>">+</button>
                        </div>
                        <button class="cart-row-remove" data-index="<?php echo $item['index']; ?>" title="Remove item">Remove</button>
                    </div>
                </div>
                <div class="cart-row-line-total" id="line-total-<?php echo $item['index']; ?>">
                    &#8377;<?php echo number_format($item['line_total']); ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <aside class="cart-summary" id="cart-summary">
            <h3>Order Summary</h3>
            <div class="summary-row">
                <span>Subtotal</span>
                <span id="summary-subtotal">&#8377;<?php echo number_format($total_price); ?></span>
            </div>
            <div class="summary-row">
                <span>Shipping</span>
                <span class="summary-shipping"><?php echo $total_price >= 999 ? 'Free' : '&#8377;99'; ?></span>
            </div>
            <div class="summary-row summary-total">
                <span>Total</span>
                <span id="summary-total">&#8377;<?php echo number_format($total_price >= 999 ? $total_price : $total_price + 99); ?></span>
            </div>
            <a href="checkout.php" class="btn-checkout">Proceed to Checkout</a>
            <p class="summary-note">Taxes calculated at checkout</p>
            <a href="index.php#products" class="btn-keep-shopping">&larr; Continue Shopping</a>
        </aside>
    </div>
    <?php endif; ?>

</main>

<footer class="footer" id="footer">
    <div class="footer-inner">
        <div class="footer-brand">
            <div class="footer-logo">Shop<span>Noir</span></div>
            <p>Your destination for curated style. Premium products, fast delivery, and effortless returns.</p>
        </div>
        <div class="footer-col">
            <h4>Shop</h4>
            <ul>
                <li><a href="#">New Arrivals</a></li>
                <li><a href="#">Best Sellers</a></li>
                <li><a href="#">Sale</a></li>
                <li><a href="#">Gift Cards</a></li>
            </ul>
        </div>
        <div class="footer-col">
            <h4>Help</h4>
            <ul>
                <li><a href="#">FAQs</a></li>
                <li><a href="#">Shipping</a></li>
                <li><a href="#">Returns</a></li>
                <li><a href="#">Size Guide</a></li>
            </ul>
        </div>
        <div class="footer-col">
            <h4>Company</h4>
            <ul>
                <li><a href="#">About Us</a></li>
                <li><a href="#">Careers</a></li>
                <li><a href="#">Privacy Policy</a></li>
                <li><a href="#">Terms</a></li>
            </ul>
        </div>
    </div>
    <div class="footer-bottom">
        <p>&copy; 2026 ShopNoir. All rights reserved.</p>
        <div class="footer-social">
            <a href="#" title="Instagram">IG</a>
            <a href="#" title="Twitter">X</a>
            <a href="#" title="Facebook">FB</a>
        </div>
    </div>
</footer>

<script>
    var navbar = document.getElementById('navbar');
    window.addEventListener('scroll', function() {
        if (window.scrollY > 10) {
            navbar.classList.add('scrolled');
        } else {
            navbar.classList.remove('scrolled');
        }
    });

    function formatINR(num) {
        return num.toLocaleString('en-IN');
    }

    function cartRequest(payload, callback) {
        var xhr = new XMLHttpRequest();
        xhr.open('POST', 'cart_api.php', true);
        xhr.setRequestHeader('Content-Type', 'application/json');
        xhr.onload = function() {
            if (xhr.status === 200) {
                try {
                    var data = JSON.parse(xhr.responseText);
                    callback(data);
                } catch(e) {
                    console.error('Cart API parse error');
                }
            }
        };
        xhr.onerror = function() {
            console.error('Cart API request failed');
        };
        xhr.send(JSON.stringify(payload));
    }

    function updateBadge(count) {
        var badge = document.getElementById('cart-badge');
        if (count > 0) {
            badge.textContent = count;
            badge.style.display = 'flex';
        } else {
            badge.style.display = 'none';
        }
    }

    function updateSummary(data) {
        updateBadge(data.total_items);

        var countEl = document.getElementById('cart-item-count');
        countEl.textContent = data.total_items + ' item' + (data.total_items !== 1 ? 's' : '');

        var subtotalEl = document.getElementById('summary-subtotal');
        var totalEl    = document.getElementById('summary-total');
        var shippingEl = document.querySelector('.summary-shipping');

        if (subtotalEl) subtotalEl.textContent = '\u20B9' + formatINR(data.total_price);

        var shipping = data.total_price >= 999 ? 0 : 99;
        if (shippingEl) shippingEl.textContent = shipping === 0 ? 'Free' : '\u20B999';
        if (totalEl) totalEl.textContent = '\u20B9' + formatINR(data.total_price + shipping);

        if (data.total_items === 0) {
            window.location.reload();
        }
    }

    function updateRow(idx, data) {
        for (var i = 0; i < data.cart.length; i++) {
            if (data.cart[i].index === idx) {
                var qtyEl = document.getElementById('qty-' + idx);
                var lineEl = document.getElementById('line-total-' + idx);
                if (qtyEl) qtyEl.textContent = data.cart[i].qty;
                if (lineEl) lineEl.textContent = '\u20B9' + formatINR(data.cart[i].line_total);
                break;
            }
        }
    }

    var minusBtns = document.querySelectorAll('.qty-minus');
    for (var i = 0; i < minusBtns.length; i++) {
        minusBtns[i].addEventListener('click', function() {
            var idx = parseInt(this.getAttribute('data-index'));
            var qtyEl = document.getElementById('qty-' + idx);
            var newQty = parseInt(qtyEl.textContent) - 1;

            if (newQty <= 0) {
                var row = document.getElementById('cart-row-' + idx);
                row.classList.add('removing');
                setTimeout(function() {
                    cartRequest({ action: 'remove', product_index: idx }, function(data) {
                        row.remove();
                        updateSummary(data);
                    });
                }, 300);
            } else {
                cartRequest({ action: 'update', product_index: idx, qty: newQty }, function(data) {
                    updateRow(idx, data);
                    updateSummary(data);
                });
            }
        });
    }

    var plusBtns = document.querySelectorAll('.qty-plus');
    for (var i = 0; i < plusBtns.length; i++) {
        plusBtns[i].addEventListener('click', function() {
            var idx = parseInt(this.getAttribute('data-index'));
            var qtyEl = document.getElementById('qty-' + idx);
            var newQty = parseInt(qtyEl.textContent) + 1;

            cartRequest({ action: 'update', product_index: idx, qty: newQty }, function(data) {
                updateRow(idx, data);
                updateSummary(data);
            });
        });
    }

    var removeBtns = document.querySelectorAll('.cart-row-remove');
    for (var i = 0; i < removeBtns.length; i++) {
        removeBtns[i].addEventListener('click', function() {
            var idx = parseInt(this.getAttribute('data-index'));
            var row = document.getElementById('cart-row-' + idx);
            row.classList.add('removing');
            setTimeout(function() {
                cartRequest({ action: 'remove', product_index: idx }, function(data) {
                    row.remove();
                    updateSummary(data);
                });
            }, 300);
        });
    }
</script>

</body>
</html>
