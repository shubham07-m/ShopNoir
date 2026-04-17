<?php
session_start();
require_once __DIR__ . '/../config/database.php';

$errors  = [];
$success = '';

$old_first_name = '';
$old_last_name  = '';
$old_email      = '';

if (!empty($db_error)) {
    $errors[] = $db_error;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = trim($_POST['first_name'] ?? '');
    $last_name  = trim($_POST['last_name'] ?? '');
    $email      = trim($_POST['email'] ?? '');
    $password   = $_POST['password'] ?? '';
    $terms      = isset($_POST['terms']);

    $old_first_name = $first_name;
    $old_last_name  = $last_name;
    $old_email      = $email;

    if (empty($first_name)) {
        $errors[] = 'First name is required.';
    }

    if (empty($last_name)) {
        $errors[] = 'Last name is required.';
    }

    if (empty($email)) {
        $errors[] = 'Email address is required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Please enter a valid email address.';
    }

    if (empty($password)) {
        $errors[] = 'Password is required.';
    } elseif (strlen($password) < 8) {
        $errors[] = 'Password must be at least 8 characters.';
    }

    if (!$terms) {
        $errors[] = 'You must agree to the Terms of Service.';
    }

    if (empty($errors)) {
        if ($pdo === null) {
            $errors[] = 'Database is not available. Please try again later.';
        } else {
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? LIMIT 1");
            $stmt->execute([$email]);

            if ($stmt->fetch()) {
                $errors[] = 'An account with this email already exists.';
            } else {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                $stmt = $pdo->prepare("INSERT INTO users (first_name, last_name, email, password) VALUES (?, ?, ?, ?)");
                $stmt->execute([$first_name, $last_name, $email, $hashed_password]);

                $success = 'Account created successfully! You can now sign in.';

                $old_first_name = '';
                $old_last_name  = '';
                $old_email      = '';
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
    <meta name="description" content="Create your ShopNoir account and start shopping the latest trends.">
    <title>Sign Up | ShopNoir</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/signup.css">
</head>
<body>

<div class="signup-wrapper">

    <div class="brand-panel">
        <div class="brand-content">
            <div class="brand-logo">Shop<span>Noir</span></div>
            <p class="brand-tagline">Curated for you</p>

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
                <h1>Create your account</h1>
                <p>Already have an account? <a href="login.php">Sign in</a></p>
            </div>

            <?php if (!empty($errors)) : ?>
                <div class="alert alert-error">
                    <?php echo htmlspecialchars($errors[0]); ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($success)) : ?>
                <div class="alert alert-success">
                    <?php echo htmlspecialchars($success); ?>
                </div>
            <?php endif; ?>

            <form action="signup.php" method="POST" id="signup-form">
                <div class="form-row">
                    <div class="form-group">
                        <input type="text" id="first-name" name="first_name" placeholder="First name"
                               value="<?php echo htmlspecialchars($old_first_name); ?>" required>
                        <label for="first-name">First name</label>
                    </div>
                    <div class="form-group">
                        <input type="text" id="last-name" name="last_name" placeholder="Last name"
                               value="<?php echo htmlspecialchars($old_last_name); ?>" required>
                        <label for="last-name">Last name</label>
                    </div>
                </div>

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

                <div class="password-strength" id="password-strength">
                    <div class="strength-bar" id="str1"></div>
                    <div class="strength-bar" id="str2"></div>
                    <div class="strength-bar" id="str3"></div>
                    <div class="strength-bar" id="str4"></div>
                </div>

                <div class="terms-group">
                    <input type="checkbox" id="terms" name="terms" required>
                    <label for="terms">I agree to the <a href="#">Terms of Service</a> and <a href="#">Privacy Policy</a></label>
                </div>

                <button type="submit" class="submit-btn" id="submit-btn">Create Account</button>
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

    // Password strength indicator
    var bar1 = document.getElementById('str1');
    var bar2 = document.getElementById('str2');
    var bar3 = document.getElementById('str3');
    var bar4 = document.getElementById('str4');
    var bars = [bar1, bar2, bar3, bar4];

    passwordInput.addEventListener('input', function() {
        var value = passwordInput.value;
        var score = 0;

        if (value.length >= 6) score++;
        if (value.length >= 10) score++;
        if (/[A-Z]/.test(value) && /[a-z]/.test(value)) score++;
        if (/[0-9]/.test(value) || /[^a-zA-Z0-9]/.test(value)) score++;

        var level = 'weak';
        if (score === 3) level = 'medium';
        if (score >= 4) level = 'strong';

        for (var i = 0; i < bars.length; i++) {
            bars[i].className = 'strength-bar';
            if (i < score) {
                bars[i].className = 'strength-bar active ' + level;
            }
        }
    });
</script>

</body>
</html>
