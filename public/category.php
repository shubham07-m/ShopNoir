<?php
session_start();
require_once __DIR__ . '/../config/products.php';

$category_name = $_GET['cat'] ?? '';

$filtered_products = [];
foreach ($products as $idx => $product) {
    if (strcasecmp($product['category'], $category_name) === 0) {
        $product['original_index'] = $idx;
        $filtered_products[] = $product;
    }
}

$is_logged_in = isset($_SESSION['user_id']);
$user_name    = $_SESSION['user_name'] ?? '';

$cart_count = 0;
if (isset($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $qty) {
        $cart_count += $qty;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="ShopNoir - <?php echo htmlspecialchars($category_name); ?> Collection">
    <title><?php echo htmlspecialchars($category_name); ?> | ShopNoir</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/index.css">
    <style>
        .category-hero {
            padding: 80px 5%;
            background: #000;
            color: #fff;
            text-align: center;
            margin-bottom: 40px;
        }
        .category-hero h1 {
            font-size: 3rem;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 10px;
        }
        .category-hero p {
            color: #888;
            font-size: 1.1rem;
        }
        .no-products {
            text-align: center;
            padding: 100px 5%;
            color: #666;
        }
        .back-link {
            display: inline-block;
            margin-top: 20px;
            color: #000;
            text-decoration: none;
            font-weight: 500;
            border-bottom: 1px solid transparent;
            transition: border-color 0.3s;
        }
        .back-link:hover {
            border-color: #000;
        }
    </style>
</head>
<body>

<nav class="navbar scrolled" id="navbar">
    <div class="nav-inner">
        <a href="index.php" class="nav-logo">Shop<span>Noir</span></a>

        <ul class="nav-links">
            <li><a href="index.php#categories">Categories</a></li>
            <li><a href="index.php#products">Shop</a></li>
            <li><a href="index.php#promo">Deals</a></li>
            <li><a href="#footer">Contact</a></li>
        </ul>

        <div class="nav-actions">
            <?php if ($is_logged_in) : ?>
                <a href="dashboard.php"><?php echo htmlspecialchars($user_name); ?></a>
                <a href="logout.php">Logout</a>
            <?php else : ?>
                <a href="login.php">Sign In</a>
                <a href="signup.php">Sign Up</a>
            <?php endif; ?>
            <a href="cart.php" class="cart-icon" id="cart-link">&#128722;<span class="cart-badge" id="cart-badge" style="<?php echo $cart_count === 0 ? 'display:none;' : ''; ?>"><?php echo $cart_count; ?></span></a>
        </div>
    </div>
</nav>

<section class="category-hero">
    <h1><?php echo htmlspecialchars($category_name ?: 'Our Collection'); ?></h1>
    <p>Discover our curated selection of premium <?php echo htmlspecialchars(strtolower($category_name)); ?></p>
</section>

<section class="products">
    <div class="container">
        <?php if (empty($filtered_products)) : ?>
            <div class="no-products">
                <h3>No products found in this category.</h3>
                <a href="index.php" class="back-link">Return to Homepage</a>
            </div>
        <?php else : ?>
            <div class="product-grid">
                <?php foreach ($filtered_products as $product) : ?>
                    <div class="product-card">
                        <?php if (!empty($product['badge'])) : ?>
                            <span class="product-badge"><?php echo htmlspecialchars($product['badge']); ?></span>
                        <?php endif; ?>

                        <a href="product.php?id=<?php echo $product['original_index']; ?>" class="product-img">
                            <img src="<?php echo htmlspecialchars($product['image']); ?>"
                                 alt="<?php echo htmlspecialchars($product['name']); ?>">
                        </a>

                        <div class="product-info">
                            <p class="product-category"><?php echo htmlspecialchars($product['category']); ?></p>
                            <h3 class="product-name"><a href="product.php?id=<?php echo $product['original_index']; ?>"><?php echo htmlspecialchars($product['name']); ?></a></h3>
                            <div class="product-bottom">
                                <span class="product-price">
                                    &#8377;<?php echo number_format($product['price']); ?>
                                    <?php if ($product['old_price']) : ?>
                                        <span class="old-price">&#8377;<?php echo number_format($product['old_price']); ?></span>
                                    <?php endif; ?>
                                </span>
                                <button class="add-to-cart-btn" data-product-index="<?php echo $product['original_index']; ?>" title="Add to Cart"></button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

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
    var cartBadge = document.getElementById('cart-badge');

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
        if (count > 0) {
            cartBadge.textContent = count;
            cartBadge.style.display = 'flex';
        } else {
            cartBadge.style.display = 'none';
        }
    }

    function addToCart(index, btn) {
        btn.classList.add('added');
        btn.textContent = '\u2713';
        setTimeout(function() {
            btn.classList.remove('added');
            btn.textContent = '';
        }, 1200);

        cartRequest({ action: 'add', product_index: index }, function(data) {
            updateBadge(data.total_items);
        });
    }

    var addBtns = document.querySelectorAll('.add-to-cart-btn');
    for (var i = 0; i < addBtns.length; i++) {
        addBtns[i].addEventListener('click', function(e) {
            e.stopPropagation();
            var index = parseInt(this.getAttribute('data-product-index'));
            addToCart(index, this);
        });
    }
</script>

</body>
</html>
