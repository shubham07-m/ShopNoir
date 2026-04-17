<?php
session_start();

$is_logged_in = isset($_SESSION['user_id']);
$user_name    = $_SESSION['user_name'] ?? '';

$cart_count = 0;
if (isset($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $qty) {
        $cart_count += $qty;
    }
}

require_once __DIR__ . '/../config/products.php';

$about_file = __DIR__ . '/../data/about_us.txt';
$about_text = file_exists($about_file) ? file_get_contents($about_file) : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <meta name="description" content="ShopNoir -- Discover curated collections of premium accessories, fashion, and lifestyle products. Free shipping on orders over Rs.999.">
    <meta name="keywords" content="ShopNoir, premium fashion, accessories, watches, sneakers, sunglasses, leather bags, online shopping India">
    <meta name="author" content="ShopNoir">
    <meta name="robots" content="index, follow">

    <meta property="og:type" content="website">
    <meta property="og:title" content="ShopNoir | Curated for You">
    <meta property="og:description" content="Discover curated collections of premium accessories, fashion, and lifestyle products.">
    <meta property="og:url" content="https://shopnoir.com">
    <meta property="og:site_name" content="ShopNoir">

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="ShopNoir | Curated for You">
    <meta name="twitter:description" content="Premium fashion & lifestyle essentials, handpicked for the modern you.">

    <title>ShopNoir | Curated for You</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="https://unpkg.com/aos@2.3.4/dist/aos.css">

    <link rel="stylesheet" href="css/index.css">
    <link rel="stylesheet" href="css/loader.css">
    <link rel="stylesheet" href="css/cursor.css">
    <link rel="stylesheet" href="css/animations.css">

    <style>
        #hero-canvas {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 0;
        }
    </style>
</head>
<body>

<div class="page-loader" id="page-loader">
    <div class="loader-logo">Shop<span>Noir</span></div>
    <div class="loader-bar-track">
        <div class="loader-bar-fill"></div>
    </div>
    <div class="loader-dots">
        <span></span>
        <span></span>
        <span></span>
    </div>
</div>

<nav class="navbar" id="navbar">
    <div class="nav-inner">
        <a href="index.php" class="nav-logo">Shop<span>Noir</span></a>

        <ul class="nav-links">
            <li><a href="#categories">Categories</a></li>
            <li><a href="#products">Shop</a></li>
            <li><a href="#about">About</a></li>
            <li><a href="#promo">Deals</a></li>
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

<section class="hero" id="hero">
    <canvas id="hero-canvas"></canvas>
    <div class="hero-content" data-aos="fade-up">
        <span class="hero-badge">&#10022; Spring Collection 2026</span>
        <h1>Discover Your <span>Signature Style</span></h1>
        <p>Curated collections of premium fashion, accessories, and lifestyle essentials — designed for the modern you.</p>
        <a href="#products" class="hero-btn">Explore Collection</a>
    </div>
</section>

<section class="categories" id="categories">
    <div class="section-header scroll-reveal">
        <h2>Shop by Category</h2>
        <p>Find exactly what you're looking for</p>
    </div>

    <div class="category-grid stagger-children">
        <a href="category.php?cat=Accessories" class="category-card" data-aos="fade-up" data-aos-delay="0">
            <div class="category-icon">&#128092;</div>
            <h3>Accessories</h3>
            <p>42 items</p>
        </a>
        <a href="category.php?cat=Footwear" class="category-card" data-aos="fade-up" data-aos-delay="50">
            <div class="category-icon">&#128095;</div>
            <h3>Footwear</h3>
            <p>38 items</p>
        </a>
        <a href="category.php?cat=Outerwear" class="category-card" data-aos="fade-up" data-aos-delay="100">
            <div class="category-icon">&#129525;</div>
            <h3>Outerwear</h3>
            <p>27 items</p>
        </a>
        <a href="category.php?cat=Watches" class="category-card" data-aos="fade-up" data-aos-delay="150">
            <div class="category-icon">&#8986;</div>
            <h3>Watches</h3>
            <p>19 items</p>
        </a>
        <a href="category.php?cat=Eyewear" class="category-card" data-aos="fade-up" data-aos-delay="200">
            <div class="category-icon">&#128374;&#65039;</div>
            <h3>Eyewear</h3>
            <p>24 items</p>
        </a>
        <a href="category.php?cat=Fragrance" class="category-card" data-aos="fade-up" data-aos-delay="250">
            <div class="category-icon">&#129524;</div>
            <h3>Fragrance</h3>
            <p>18 items</p>
        </a>
        <a href="category.php?cat=Audio" class="category-card" data-aos="fade-up" data-aos-delay="300">
            <div class="category-icon">&#127911;</div>
            <h3>Audio</h3>
            <p>15 items</p>
        </a>
    </div>
</section>

<section class="products" id="products">
    <div class="section-header scroll-reveal">
        <h2>Featured Products</h2>
        <p>Handpicked essentials for your collection</p>
    </div>

    <div class="product-grid stagger-children">
        <?php foreach ($products as $idx => $product) : ?>
            <div class="product-card" data-aos="fade-up" data-aos-delay="<?php echo ($idx % 4) * 80; ?>">
                <?php if (!empty($product['badge'])) : ?>
                    <span class="product-badge"><?php echo htmlspecialchars($product['badge']); ?></span>
                <?php endif; ?>

                <a href="product.php?id=<?php echo $idx; ?>" class="product-img">
                    <img src="<?php echo htmlspecialchars($product['image']); ?>"
                         alt="<?php echo htmlspecialchars($product['name']); ?>"
                         loading="lazy">
                </a>

                <div class="product-info">
                    <p class="product-category"><?php echo htmlspecialchars($product['category']); ?></p>
                    <h3 class="product-name"><a href="product.php?id=<?php echo $idx; ?>"><?php echo htmlspecialchars($product['name']); ?></a></h3>
                    <div class="product-bottom">
                        <span class="product-price">
                            &#8377;<?php echo number_format($product['price']); ?>
                            <?php if ($product['old_price']) : ?>
                                <span class="old-price">&#8377;<?php echo number_format($product['old_price']); ?></span>
                            <?php endif; ?>
                        </span>
                        <button class="add-to-cart-btn" data-product-index="<?php echo $idx; ?>" title="Add to Cart"></button>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<?php if (!empty($about_text)) : ?>
<section class="about-section" id="about">
    <div class="section-header scroll-reveal">
        <h2>About Us</h2>
        <p>The story behind ShopNoir</p>
    </div>

    <div class="about-content">
        <div class="about-text scroll-reveal" data-aos="fade-right">
            <?php
            $paragraphs = preg_split('/\n\s*\n|\.\s+/', $about_text, -1, PREG_SPLIT_NO_EMPTY);
            if (count($paragraphs) <= 1) {
                echo '<p>' . nl2br(htmlspecialchars(trim($about_text))) . '</p>';
            } else {
                foreach ($paragraphs as $para) {
                    $para = trim($para);
                    if ($para !== '') {
                        if (!preg_match('/[.!?]$/', $para)) {
                            $para .= '.';
                        }
                        echo '<p>' . htmlspecialchars($para) . '</p>';
                    }
                }
            }
            ?>
        </div>

        <div class="about-stats stagger-children" data-aos="fade-left">
            <div class="stat-card">
                <div class="stat-number"><?php echo count($products); ?>+</div>
                <div class="stat-label">Products</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">50+</div>
                <div class="stat-label">Cities</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">4.9</div>
                <div class="stat-label">Rating</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">Free</div>
                <div class="stat-label">Shipping 999+</div>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<section class="promo-banner parallax-section" id="promo">
    <div class="promo-inner" data-aos="zoom-in">
        <h2>Get 20% Off Your First Order</h2>
        <p>Join ShopNoir today and enjoy exclusive member-only discounts, early access to new drops, and free shipping on orders over &#8377;999.</p>
        <a href="signup.php" class="promo-btn">Join Now — It's Free</a>
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
                <li><a href="#about">About Us</a></li>
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
            <a href="#" title="YouTube">YT</a>
        </div>
    </div>
</footer>

<script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>
<script src="https://unpkg.com/@floating-ui/core@1.6.0/dist/floating-ui.core.umd.min.js"></script>
<script src="https://unpkg.com/@floating-ui/dom@1.6.3/dist/floating-ui.dom.umd.min.js"></script>
<script src="js/loader.js"></script>
<script src="js/hero_bg.js"></script>
<script src="js/cursor.js"></script>
<script src="js/animations.js"></script>
<script src="js/cart_effect.js"></script>

</body>
</html>
