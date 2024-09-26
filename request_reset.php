<?php
header("Strict-Transport-Security: max-age=63072000; includeSubDomains; preload"); // HSTS
header("Content-Security-Policy: default-src 'self'; style-src 'self' 'https://cdnjs.cloudflare.com' 'unsafe-inline'; font-src 'self' 'https://cdnjs.cloudflare.com'; script-src 'self';");
header("X-Content-Type-Options: nosniff"); // Prevent MIME type sniffing
header("X-XSS-Protection: 1; mode=block"); // XSS protection
header("X-Frame-Options: DENY"); // Prevent clickjacking
header("Referrer-Policy: no-referrer"); // Control referrer information

// Display errors for development (remove in production)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
include 'partials/_dbconnect.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

// Include PHPMailer classes
require 'PHPMailer-master/src/PHPMailer.php';
require 'PHPMailer-master/src/Exception.php';
require 'PHPMailer-master/src/SMTP.php';

// Rate limiting settings
$maxAttempts = 5; // Maximum attempts allowed
$timeFrame = 300; // Time frame in seconds (5 minutes)

// Check if the user has exceeded the limit
if (isset($_SESSION['attempts']) && $_SESSION['attempts'] >= $maxAttempts) {
    $remainingTime = $_SESSION['lockout_time'] - time();
    if ($remainingTime > 0) {
        die('Too many requests. Please try again in ' . $remainingTime . ' seconds.');
    } else {
        unset($_SESSION['attempts']);
        unset($_SESSION['lockout_time']);
    }
}

// Generate CSRF token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Handle POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate CSRF token
    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        die('CSRF token validation failed.');
    }

    // Sanitize email input
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo '<div class="alert alert-warning" role="alert">
                <strong>Warning!</strong> Invalid email address.
              </div>';
        exit;
    }

    // Check if the email exists in the users table
    $stmt = $conn->prepare("SELECT id FROM merapyareusers WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // Generate a unique token
        $token = bin2hex(random_bytes(32));

        // Store the token in the password_resets table
        $stmt = $conn->prepare("INSERT INTO password_resets (email, token) VALUES (?, ?)");
        $stmt->bind_param("ss", $email, $token);
        $stmt->execute();

        // Send the password reset email
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.hostinger.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'no.reply@shopsager.in';
            $mail->Password = '0Qg$xJhBF[7b'; // Replace with your actual SMTP password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            // Recipients
            $mail->setFrom('no.reply@shopsager.in', 'ShopSAGE');
            $mail->addAddress($email);

            // Content
            $mail->isHTML(true);
            $mail->Subject = 'Password Reset Request';
            $mail->Body = '
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            color: #333333;
        }
        .email-container {
            width: 100%;
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            padding: 20px;
        }
        .email-header {
            background: linear-gradient(to right, #ff7e5f, #feb47b);
            padding: 25px;
            text-align: center;
            color: #ffffff;
                                        border-radius: 10px;

        }
        .email-header h1 {
            margin: 0;
            font-size: 24px;
            letter-spacing: 1px;
            text-transform: uppercase;
        }
        .email-body {
            padding: 30px;
            text-align: center;
        }
        .email-body h2 {
            color: #ff6f00;
            font-size: 22px;
            margin-bottom: 20px;
            font-weight: 600;
        }
        .email-body p {
            font-size: 16px;
            line-height: 1.6;
            color: #666666;
            margin-bottom: 30px;
        }
        .email-body a {
            display: inline-block;
            padding: 12px 24px;
            background-color: #ff6f00;
            color: #ffffff;
            text-decoration: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: bold;
            transition: background 0.3s ease;
        }
        .email-body a:hover {
            background-color: #e65100;
        }
        .email-footer {
            padding: 20px;
            text-align: center;
            font-size: 14px;
            color: #ffffff;
            background-color: #333333;
            border-radius: 10px;

            border-top: 1px solid #dddddd;
        }
        .email-footer p {
            margin: 0;
        }
            
        .email-footer p a {
            color: #ff6f00;
            text-decoration: none;
        }
        .email-footer p a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="email-header">
            <h1>Password Reset Request</h1>
        </div>
        <div class="email-body">
            <h2>Hello Buddy,</h2>
            <p>It has come to our attention that a password reset has been requested. Please click the button below to proceed with resetting your password. If this request was not initiated by you, we kindly ask that you disregard this email..</p>
            <a href="http://localhost/New-project/reset_password_form.php?token=' . $token . '">Reset Password</a>

        </div>
        <div class="email-footer">
            <p>&copy; ' . date("Y") . ' ShopSAGE. All rights reserved.</p>
            <p>Need help? <a href="mailto:support@shopsage.com">Contact Support</a></p>

        </div>
    </div>
</body>
</html>
';


$mail->send();
echo '<div class="alert alert-success" role="alert">
        <strong>Success!</strong> A password reset link has been sent to your email. Please check your inbox.
      </div>';

// Rate limiting: track attempts
$_SESSION['attempts'] = isset($_SESSION['attempts']) ? $_SESSION['attempts'] + 1 : 1;
} catch (Exception $e) {
echo '<div class="alert alert-danger" role="alert">
        <strong>Error!</strong> Message could not be sent. Mailer Error: ' . htmlspecialchars($mail->ErrorInfo) . '
      </div>';
}
} else {
echo '<div class="alert alert-warning" role="alert">
    <strong>Warning!</strong> No account found with that email.
  </div>';

// Increment attempts
$_SESSION['attempts'] = isset($_SESSION['attempts']) ? $_SESSION['attempts'] + 1 : 1;
}

// Lockout logic
if (isset($_SESSION['attempts']) && $_SESSION['attempts'] >= $maxAttempts) {
$_SESSION['lockout_time'] = time() + $timeFrame; // Set lockout time
die('Too many requests. Please try again in ' . $timeFrame . ' seconds.');
}
}
?>

<!DOCTYPE html>
<html lang="en">
<head>

<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="robots" content="noindex, nofollow"> <!-- Prevent indexing for sensitive pages -->
    <meta http-equiv="Content-Security-Policy" content="default-src 'self'; script-src 'self'; object-src 'none'; base-uri 'self'; form-action 'self';"> <!-- CSP -->
    <meta http-equiv="X-Content-Type-Options" content="nosniff"> <!-- Prevent MIME type sniffing -->
    <meta http-equiv="X-XSS-Protection" content="1; mode=block"> <!-- XSS protection -->
    <meta name="referrer" content="no-referrer"> <!-- Control referrer information -->
    <title>Document</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(to right, #ff7e5f, #feb47b);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            overflow: hidden;
        }
        .container {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
            padding: 2rem;
            max-width: 360px;
            width: 100%;
            animation: slideIn 1s ease-out;
        }
        h2 {
            color: #ff6f00;
            text-align: center;
            margin-bottom: 1.5rem;
            font-size: 1.75rem;
            font-weight: 600;
        }
        label {
            display: block;
            font-size: 1rem;
            color: #555;
            margin-bottom: 0.5rem;
        }
        input[type="email"] {
            width: calc(100% - 22px);
            padding: 12px;
            margin-bottom: 1rem;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 1rem;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }
        input[type="email"]:focus {
            border-color: #ff6f00;
            box-shadow: 0 0 8px rgba(255, 111, 0, 0.3);
            outline: none;
        }
        button {
            background: #ff6f00;
            color: #fff;
            border: none;
            border-radius: 8px;
            padding: 12px 0;
            font-size: 1.1rem;
            cursor: pointer;
            transition: background 0.3s ease, transform 0.3s ease;
            width: 100%;
        }
        button:hover {
            background: #e65100;
            transform: scale(1.02);
        }
        button:active {
            transform: scale(0.98);
        }
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Responsive styles */
        @media (max-width: 480px) {
            .container {
                padding: 1.5rem;
                max-width: 90%;
            }
            h2 {
                font-size: 1.5rem;
            }
            input[type="email"] {
                width: calc(100% - 20px);
            }
            button {
                font-size: 1rem;
            }
        }
    </style>
</head>
<body>
<div class="container">
        <h2>Forgot Your Password?</h2>
        <form action="request_reset.php" method="POST">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

            <label for="email">Enter your email address:</label>
            <input type="email" name="email" id="email" required>
            <button type="submit">Request Password Reset</button>
        </form>
    </div>
</body>
</html>