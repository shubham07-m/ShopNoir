<?php
require_once __DIR__ . '/../middleware/admin_auth.php';

$json_path = __DIR__ . '/../data/products.json';

$idx = isset($_GET['id']) ? intval($_GET['id']) : -1;

if ($idx < 0) {
    $_SESSION['admin_error'] = 'Invalid product ID.';
    header('Location: admin_manage_products.php');
    exit;
}

$products = [];
if (file_exists($json_path)) {
    $products = json_decode(file_get_contents($json_path), true);
    if (!is_array($products)) $products = [];
}

if (!isset($products[$idx])) {
    $_SESSION['admin_error'] = 'Product not found.';
    header('Location: admin_manage_products.php');
    exit;
}

$deleted_name = $products[$idx]['name'] ?? 'Unknown';

array_splice($products, $idx, 1);

$written = file_put_contents($json_path, json_encode($products, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

if ($written !== false) {
    $_SESSION['admin_success'] = 'Product "' . $deleted_name . '" has been deleted.';
} else {
    $_SESSION['admin_error'] = 'Failed to delete product. Check file permissions.';
}

header('Location: admin_manage_products.php');
exit;
