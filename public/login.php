<?php
session_start();
require_once __DIR__ . '/../config/database.php';

$errors = [];
$old_email = '';

if (!empty($db_error)) {
    $errors[] = $db_error;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $old_email = $email;

    if (empty($email)) {
        $errors[] = 'Email address is required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Please enter a valid email address.';
    }

    if (empty($password)) {
        $errors[] = 'Password is required.';
    }

    if (empty($errors)) {
        if ($pdo === null) {
            $errors[] = 'Database is not available.';
        } else {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id']    = $user['id'];
                $_SESSION['user_name']  = $user['first_name'] . ' ' . $user['last_name'];
                $_SESSION['user_email'] = $user['email'];

                $admin_emails = [
                    'admin@shopnoir.com',
                    'shubham@shopnoir.com',
                ];
                $is_admin = in_array(strtolower($user['email']), array_map('strtolower', $admin_emails));

                if ($is_admin) {
                    $redirect = 'admin_dashboard.php';
                    unset($_SESSION['redirect_after_login']);
                } else {
                    // Redirect to saved page or dashboard
                    $redirect = $_SESSION['redirect_after_login'] ?? 'dashboard.php';
                    unset($_SESSION['redirect_after_login']);
                }

                header("Location: $redirect");
                exit;
            } else {
                $errors[] = 'Invalid email or password.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Sign in to your ShopNoir account.">
    <title>Login | ShopNoir</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/login.css">
</head>
<body>

<div class="login-wrapper">

    <div class="interactive-panel">
        <div class="panel-overlay">
            <div class="brand-logo">Shop<span>Noir</span></div>
            <p class="brand-sub">Welcome back</p>

            <ul class="brand-features">
                <li><span class="icon">&#10022;</span> Exclusive collections updated weekly</li>
                <li><span class="icon">&#9889;</span> Lightning-fast delivery worldwide</li>
                <li><span class="icon">&#9851;</span> Hassle-free returns within 30 days</li>
                <li><span class="icon">&#9733;</span> Members-only deals &amp; early access</li>
            </ul>
        </div>
    </div>

    <div class="form-panel">
        <div class="form-container">

            <div class="form-header">
                <h1>Sign in</h1>
                <p>Don't have an account? <a href="signup.php">Create one</a></p>
            </div>

            <?php if (!empty($errors)) : ?>
                <div class="alert alert-error">
                    <?php echo htmlspecialchars($errors[0]); ?>
                </div>
            <?php endif; ?>

            <form action="login.php" method="POST" id="login-form">
                <div class="form-group">
                    <input type="email" id="email" name="email" placeholder="Email address"
                           value="<?php echo htmlspecialchars($old_email); ?>" required>
                    <label for="email">Email address</label>
                </div>

                <div class="form-group">
                    <div class="password-wrapper">
                        <input type="password" id="password" name="password" placeholder="Password" required>
                        <label for="password">Password</label>
                        <button type="button" class="toggle-password" id="toggle-password">Show</button>
                    </div>
                </div>

                <div class="form-options">
                    <div class="remember-me">
                        <input type="checkbox" id="remember" name="remember">
                        <label for="remember">Remember me</label>
                    </div>
                    <a href="#" class="forgot-link">Forgot password?</a>
                </div>

                <button type="submit" class="submit-btn" id="submit-btn">Sign In</button>
            </form>

            <p class="form-footer">&copy; 2026 ShopNoir. All rights reserved.</p>
        </div>
    </div>

</div>

<script>
    var toggleBtn = document.getElementById('toggle-password');
    var passwordInput = document.getElementById('password');

    toggleBtn.addEventListener('click', function() {
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            toggleBtn.textContent = 'Hide';
        } else {
            passwordInput.type = 'password';
            toggleBtn.textContent = 'Show';
        }
    });
</script>

</body>
</html>
