<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$admin_emails = [
    'admin@shopnoir.com',
    'shubham@shopnoir.com',
];

if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_email'])) {
    header('Location: login.php');
    exit;
}

$is_admin = in_array(strtolower($_SESSION['user_email']), array_map('strtolower', $admin_emails));

if (!$is_admin) {
    http_response_code(403);
    echo '<!DOCTYPE html><html><head><title>Access Denied</title>';
    echo '<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">';
    echo '<style>body{font-family:"Inter",sans-serif;background:#0c0c0f;color:#e0e0e0;display:flex;align-items:center;justify-content:center;min-height:100vh;margin:0;text-align:center;}';
    echo '.denied{max-width:400px;}.denied h1{font-size:4rem;margin:0;}.denied h2{font-size:1.2rem;margin:0.5rem 0 1rem;color:#888;}.denied p{font-size:0.85rem;color:#555;margin-bottom:1.5rem;}';
    echo '.denied a{display:inline-block;padding:0.65rem 1.5rem;background:linear-gradient(135deg,#6366f1,#8b5cf6);color:#fff;border-radius:10px;font-weight:600;font-size:0.85rem;text-decoration:none;}</style></head>';
    echo '<body><div class="denied"><h1>403</h1><h2>Access Denied</h2>';
    echo '<p>You do not have admin privileges. This area is restricted to authorized administrators only.</p>';
    echo '<a href="index.php">&larr; Back to Store</a></div></body></html>';
    exit;
}
?>
