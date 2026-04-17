<?php
session_start();

require_once __DIR__ . '/../config/products.php';

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

header('Content-Type: application/json');

$input  = json_decode(file_get_contents('php://input'), true);
$action = $input['action'] ?? '';

switch ($action) {

    case 'add':
        $idx = intval($input['product_index'] ?? -1);
        if ($idx < 0 || $idx >= count($products)) {
            echo json_encode(['success' => false, 'message' => 'Invalid product.']);
            exit;
        }
        if (isset($_SESSION['cart'][$idx])) {
            $_SESSION['cart'][$idx]++;
        } else {
            $_SESSION['cart'][$idx] = 1;
        }
        break;

    case 'remove':
        $idx = intval($input['product_index'] ?? -1);
        unset($_SESSION['cart'][$idx]);
        break;

    case 'update':
        $idx = intval($input['product_index'] ?? -1);
        $qty = intval($input['qty'] ?? 0);
        if ($qty <= 0) {
            unset($_SESSION['cart'][$idx]);
        } else {
            $_SESSION['cart'][$idx] = $qty;
        }
        break;

    case 'get':
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Unknown action.']);
        exit;
}

$cart_items   = [];
$total_items  = 0;
$total_price  = 0;

foreach ($_SESSION['cart'] as $idx => $qty) {
    if (!isset($products[$idx])) continue;

    $product      = $products[$idx];
    $line_total   = $product['price'] * $qty;
    $total_items += $qty;
    $total_price += $line_total;

    $cart_items[] = [
        'index'      => $idx,
        'name'       => $product['name'],
        'category'   => $product['category'],
        'price'      => $product['price'],
        'image'      => $product['image'],
        'qty'        => $qty,
        'line_total' => $line_total,
    ];
}

echo json_encode([
    'success'     => true,
    'cart'        => $cart_items,
    'total_items' => $total_items,
    'total_price' => $total_price,
]);
