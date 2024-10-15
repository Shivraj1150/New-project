<?php
session_start();
// Add Security Headers
// 1. Content Security Policy (CSP)
header("Content-Security-Policy: default-src 'self'; style-src 'self' 'unsafe-inline'; script-src 'self'; img-src 'self' data:; font-src 'self' data:; object-src 'none'; base-uri 'self'; form-action 'self'; frame-ancestors 'none'; upgrade-insecure-requests; block-all-mixed-content");

// 2. Referrer Policy
header("Referrer-Policy: no-referrer");

// 3. X-Content-Type-Options
header("X-Content-Type-Options: nosniff");

// 4. X-Frame-Options
header("X-Frame-Options: DENY");

// 5. X-Permitted-Cross-Domain-Policies
header("X-Permitted-Cross-Domain-Policies: none");

// 6. X-XSS-Protection (for older browsers, though CSP is preferred)
header("X-XSS-Protection: 1; mode=block");

// 7. Permissions Policy (Feature Policy)
header("Permissions-Policy: geolocation=(), microphone=(), camera=(), fullscreen=(), payment=(), sync-xhr=(), usb=(), magnetometer=(), gyroscope=(), accelerometer=(), ambient-light-sensor=()");

// Ensure you use HTTPS (HSTS can only be set from the server)
header("Strict-Transport-Security: max-age=31536000; includeSubDomains; preload");

// Max login attempts and lockout settings
$max_attempts = 5;
$lockout_time = 10 * 60; // 10 minutes
$captcha_enabled = false; // Assume CAPTCHA is disabled by default

if (!isset($_SESSION['attempts'])) {
    $_SESSION['attempts'] = 0;
    $_SESSION['last_attempt_time'] = time();
}

// Check if the user is locked out
if ($_SESSION['attempts'] >= $max_attempts && (time() - $_SESSION['last_attempt_time']) < $lockout_time) {
    // Optionally enable CAPTCHA after several failed attempts
    $captcha_enabled = true;
    die("Too many login attempts. Please try again after 10 minutes or solve CAPTCHA.");
}

// Use a hashed password for better security
$stored_username = 'admin';
$stored_password_hash = password_hash('mypassword', PASSWORD_BCRYPT); // Use bcrypt for stronger hashing

// CSRF token handling
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        die("Invalid CSRF token");
    }

    $input_username = $_POST['username'];
    $input_password = $_POST['password'];

    if ($input_username === $stored_username && password_verify($input_password, $stored_password_hash)) {
        // Regenerate session ID to prevent session fixation attacks
        session_regenerate_id(true);

        // Start session and store the user authentication
        $_SESSION['loggedin'] = true;
        $_SESSION['attempts'] = 0;  // Reset attempts on successful login
        header('Location: admin.php');  // Redirect to the admin page
        exit();
    } else {
        $_SESSION['attempts'] += 1;
        $_SESSION['last_attempt_time'] = time();
        echo "Invalid login details";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Security-Policy" content="
        default-src 'self'; 
        script-src 'self'; 
        style-src 'self' 'unsafe-inline'; 
        img-src 'self' data:; 
        font-src 'self' data:; 
        object-src 'none'; 
        base-uri 'self'; 
        form-action 'self'; 
        frame-ancestors 'none'; 
        upgrade-insecure-requests; 
        block-all-mixed-content;
    ">
    
    <!-- Referrer Policy -->
    <meta name="referrer" content="no-referrer">

    <!-- X-Content-Type-Options -->
    <meta http-equiv="X-Content-Type-Options" content="nosniff">

    <!-- X-Frame-Options -->
    <meta http-equiv="X-Frame-Options" content="DENY">

    <!-- X-Permitted-Cross-Domain-Policies -->
    <meta http-equiv="X-Permitted-Cross-Domain-Policies" content="none">

    <!-- X-XSS-Protection (Outdated but still used by some browsers) -->
    <meta http-equiv="X-XSS-Protection" content="1; mode=block">

    <!-- Permissions Policy (formerly Feature Policy) -->
    <meta http-equiv="Permissions-Policy" content="
        geolocation=(), 
        microphone=(), 
        camera=(), 
        fullscreen=(), 
        payment=(), 
        sync-xhr=(),
        usb=(),
        magnetometer=(),
        gyroscope=(),
        accelerometer=(),
        ambient-light-sensor=()
    ">
    <title>Admin Login</title>
    <style>
        body {
            background-color: #f4f7f6;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        form {
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        input {
            display: block;
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 8px;
            border: 1px solid #ddd;
        }
        button {
            padding: 10px;
            background-color: #ff6b35;
            color: white;
            border: none;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <form method="POST" action="login.php">
        <h2>Admin Login</h2>
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
        <?php if ($captcha_enabled): ?>
            <!-- Add CAPTCHA code here -->
            <p>Please solve the CAPTCHA.</p>
        <?php endif; ?>
        <button type="submit">Login</button>
    </form>
</body>
</html>