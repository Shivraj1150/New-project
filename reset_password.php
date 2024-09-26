<?php
session_start();
include 'partials/_dbconnect.php';

// Security Headers
header("Strict-Transport-Security: max-age=63072000; includeSubDomains; preload"); // HSTS
header("Content-Security-Policy: default-src 'self'; style-src 'self' 'https://cdnjs.cloudflare.com' 'unsafe-inline'; font-src 'self' 'https://cdnjs.cloudflare.com'; script-src 'self';");
header("X-Content-Type-Options: nosniff"); // Prevent MIME type sniffing
header("X-XSS-Protection: 1; mode=block"); // XSS protection
header("X-Frame-Options: DENY"); // Prevent clickjacking
header("Referrer-Policy: no-referrer"); // Control referrer information

$resetSuccess = false;
$errorMessage = '';

if (isset($_GET['token'])) {
    // Sanitize and validate token
    $token = htmlspecialchars($_GET['token'], ENT_QUOTES, 'UTF-8');

    // Validate token format (assuming a 64-character hex token)
    if (!preg_match('/^[a-f0-9]{64}$/', $token)) {
        die('Invalid token format.');
    }

    // Check if token exists and retrieve email
    $stmt = $conn->prepare("SELECT email, created_at FROM password_resets WHERE token = ?");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($email, $created_at);
        $stmt->fetch();

        // Token expiration check (e.g., 1-hour expiry)
        $expiry_time = strtotime($created_at) + 3600; // Token valid for 1 hour
        if (time() > $expiry_time) {
            $errorMessage = 'Token has expired. Please request a new password reset.';
        } else if (isset($_POST['password'], $_POST['csrf_token'])) {
            // CSRF token validation
            if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
                die('CSRF token validation failed.');
            }

            // Password strength validation (at least 8 characters, upper/lowercase, digit, special character)
            $password = $_POST['password'];
            $passwordRegex = '/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[@$!%*?&#]).{8,}$/';
            if (!preg_match($passwordRegex, $password)) {
                $errorMessage = 'Password must contain at least 8 characters, including an uppercase letter, a lowercase letter, a number, and a special character.';
            } else {
                // Hash the password
                $new_password = password_hash($password, PASSWORD_DEFAULT);

                // Update the user's password
                $stmt = $conn->prepare("UPDATE merapyareusers SET password = ? WHERE email = ?");
                $stmt->bind_param("ss", $new_password, $email);
                if ($stmt->execute()) {
                    // Remove token from the database after successful reset
                    $stmt = $conn->prepare("DELETE FROM password_resets WHERE token = ?");
                    $stmt->bind_param("s", $token);
                    $stmt->execute();

                    $resetSuccess = true;
                } else {
                    $errorMessage = 'Failed to update password. Please try again.';
                }
            }
        }
    } else {
        $errorMessage = 'Invalid or expired token.';
    }
} else {
    $errorMessage = 'No token provided.';
}

// Generate CSRF token for form
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Reset</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #fff5e6 !important;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            overflow: hidden;
        }
        .container {
            background: #ffffff;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 20px 30px rgba(0, 0, 0, 0.1);
            text-align: center;
            max-width: 400px;
            width: 100%;
            opacity: 0;
            transform: translateY(20px);
            animation: fadeInUp 1s ease forwards;
        }
        .container h2 {
            margin-bottom: 20px;
            color: #ff6f00;
            font-size: 24px;
        }
        .container p {
            margin: 15px 0;
            color: #333;
            font-size: 16px;
        }
        .btn {
            display: inline-block;
            padding: 12px 25px;
            font-size: 16px;
            color: white;
            background: #ff6f00;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            transition: background 0.3s ease, transform 0.3s ease;
            margin-top: 20px;
        }
        .btn:hover {
            background: #e65c00;
            transform: translateY(-2px);
        }
        .error-message {
            color: red;
            margin: 20px 0;
        }
        @keyframes fadeInUp {
            0% {
                opacity: 0;
                transform: translateY(20px);
            }
            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <?php if ($resetSuccess): ?>
            <h2>Password Reset Successful</h2>
            <p>Your password has been updated.</p>
        <?php else: ?>
            <h2>Password Reset Failed</h2>
            <p class="error-message"><?php echo htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8'); ?></p>
            <!-- Form for password reset -->
            <form method="POST">
                <input type="password" name="password" placeholder="New Password" required>
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                <button type="submit" class="btn">Reset Password</button>
            </form>
        <?php endif; ?>
        <a href="_login.php" class="btn"><i class="fas fa-sign-in-alt"></i> Back to Login</a>
    </div>
</body>
</html>
