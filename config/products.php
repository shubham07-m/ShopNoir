<?php

$json_path = __DIR__ . '/../data/products.json';

if (file_exists($json_path)) {
    $json_content = file_get_contents($json_path);
    $products = json_decode($json_content, true);
    if (!is_array($products)) {
        $products = [];
    }
} else {
    $products = [];
}
?>
