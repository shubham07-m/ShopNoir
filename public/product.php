<?php
session_start();
require_once __DIR__ . '/../config/products.php';

$product_id = isset($_GET['id']) ? intval($_GET['id']) : -1;

if ($product_id < 0 || !isset($products[$product_id])) {
    header('Location: index.php');
    exit;
}

$product = $products[$product_id];

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
    <meta name="description" content="ShopNoir - <?php echo htmlspecialchars($product['name']); ?>">
    <title><?php echo htmlspecialchars($product['name']); ?> | ShopNoir</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/index.css">
    <style>
        .product-detail-container {
            max-width: 1200px;
            margin: 120px auto 60px;
            padding: 0 5%;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 60px;
        }

        .product-gallery {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .main-img {
            width: 100%;
            height: 500px;
            background: #f5f5f5;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            border-radius: 8px;
        }

        .main-img img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .thumb-grid {
            display: flex;
            gap: 10px;
        }

        .thumb {
            width: 80px;
            height: 80px;
            background: #f5f5f5;
            cursor: pointer;
            border: 2px solid transparent;
            border-radius: 4px;
            overflow: hidden;
        }

        .thumb.active {
            border-color: #000;
        }

        .thumb img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .product-meta {
            display: flex;
            flex-direction: column;
            gap: 24px;
        }

        .product-meta .category {
            text-transform: uppercase;
            font-size: 0.9rem;
            letter-spacing: 1px;
            color: #888;
        }

        .product-meta h1 {
            font-size: 2.5rem;
            margin: 0;
        }

        .product-meta .price {
            font-size: 1.8rem;
            font-weight: 600;
        }

        .product-meta .old-price {
            text-decoration: line-through;
            color: #888;
            font-size: 1.2rem;
            margin-left: 10px;
        }

        .product-meta .description {
            line-height: 1.8;
            color: #444;
        }

        .add-to-cart-large {
            padding: 16px 32px;
            background: #000;
            color: #fff;
            border: none;
            cursor: pointer;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: background 0.3s;
            margin-top: 20px;
        }

        .add-to-cart-large:hover {
            background: #333;
        }

        .reviews-section {
            max-width: 1200px;
            margin: 0 auto 100px;
            padding: 60px 5%;
            border-top: 1px solid #eee;
        }

        .reviews-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 40px;
        }

        .review-card {
            padding: 24px 0;
            border-bottom: 1px solid #f9f9f9;
        }

        .review-user {
            font-weight: 600;
            margin-bottom: 4px;
        }

        .review-rating {
            color: #ffcc00;
            margin-bottom: 12px;
        }

        .review-comment {
            color: #555;
            line-height: 1.6;
        }

        .review-date {
            font-size: 0.8rem;
            color: #999;
            margin-top: 12px;
        }

        @media (max-width: 768px) {
            .product-detail-container {
                grid-template-columns: 1fr;
                gap: 40px;
                margin-top: 80px;
            }
            .main-img {
                height: 350px;
            }
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

<section class="product-detail-container">
    <div class="product-gallery">
        <div class="main-img">
            <img src="<?php echo htmlspecialchars($product['image']); ?>" id="target-img" alt="<?php echo htmlspecialchars($product['name']); ?>">
        </div>
        <?php if (!empty($product['photos'])) : ?>
            <div class="thumb-grid">
                <?php foreach ($product['photos'] as $photo) : ?>
                    <div class="thumb" onclick="changeImage('<?php echo htmlspecialchars($photo); ?>', this)">
                        <img src="<?php echo htmlspecialchars($photo); ?>" alt="thumbnail">
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <div class="product-meta">
        <span class="category"><?php echo htmlspecialchars($product['category']); ?></span>
        <h1><?php echo htmlspecialchars($product['name']); ?></h1>
        <div class="price">
            &#8377;<?php echo number_format($product['price']); ?>
            <?php if ($product['old_price']) : ?>
                <span class="old-price">&#8377;<?php echo number_format($product['old_price']); ?></span>
            <?php endif; ?>
        </div>
        <p class="description">
            <?php echo nl2br(htmlspecialchars($product['description'] ?? 'No description available for this item.')); ?>
        </p>
        <button class="add-to-cart-large" onclick="addToCart(<?php echo $product_id; ?>, this)">Add to Cart</button>
    </div>
</section>

<section class="reviews-section">
    <div class="reviews-header">
        <h2>Customer Reviews (<?php echo count($product['reviews'] ?? []); ?>)</h2>
    </div>

    <?php if (empty($product['reviews'])) : ?>
        <p style="color:#888;">No reviews yet. Be the first to review!</p>
    <?php else : ?>
        <div class="reviews-list">
            <?php foreach ($product['reviews'] as $review) : ?>
                <div class="review-card">
                    <div class="review-user"><?php echo htmlspecialchars($review['user']); ?></div>
                    <div class="review-rating">
                        <?php for ($i = 0; $i < 5; $i++) echo $i < $review['rating'] ? '&#9733;' : '&#9734;'; ?>
                    </div>
                    <p class="review-comment"><?php echo htmlspecialchars($review['comment']); ?></p>
                    <div class="review-date"><?php echo htmlspecialchars($review['date']); ?></div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>

<footer class="footer" id="footer">
    <div class="footer-inner">
        <div class="footer-brand">
            <div class="footer-logo">Shop<span>Noir</span></div>
            <p>Your destination for curated style. Premium products, fast delivery, and effortless returns.</p>
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
        var originalText = btn.textContent;
        btn.textContent = 'Adding...';
        btn.disabled = true;

        cartRequest({ action: 'add', product_index: index }, function(data) {
            updateBadge(data.total_items);
            btn.textContent = '\u2713 Added';
            setTimeout(function() {
                btn.textContent = originalText;
                btn.disabled = false;
            }, 1500);
        });
    }

    function changeImage(src, thumb) {
        document.getElementById('target-img').src = src;
        var thumbs = document.querySelectorAll('.thumb');
        thumbs.forEach(t => t.classList.remove('active'));
        thumb.classList.add('active');
    }
</script>

</body>
</html>
