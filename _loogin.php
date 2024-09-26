<?php
// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start([
    'cookie_lifetime' => 86400, // Adjust as needed
    'cookie_secure' => true,    // Ensure cookies are sent over HTTPS
    'cookie_httponly' => true,  // Prevents JavaScript access to cookies
    'use_strict_mode' => true,
    'use_cookies' => true,
    'use_only_cookies' => true
]);

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;
require 'PHPMailer-master/src/PHPMailer.php';
require 'PHPMailer-master/src/Exception.php';
require 'PHPMailer-master/src/SMTP.php';

// Database connection
include 'partials/_dbconnect.php';

// Initialize the message variable
$message = "";
// Secure headers to prevent clickjacking and XSS
header("Content-Security-Policy: default-src 'self'; script-src 'self'; style-src 'self' https://cdn.jsdelivr.net; object-src 'none'; frame-ancestors 'none';");
header("X-Frame-Options: DENY");
header("X-Content-Type-Options: nosniff");
header("X-XSS-Protection: 1; mode=block");
header("Referrer-Policy: no-referrer");
header("Strict-Transport-Security: max-age=31536000; includeSubDomains; preload");
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Referrer-Policy: no-referrer-when-downgrade");
header("Pragma: no-cache");
header("Expires: 0");


// CSRF Token Generation
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Sign-Up form processing
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['signUp'])) {
    // Validate CSRF token
    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        die("CSRF validation failed.");
    }

    // Input sanitization and validation
    $firstName = filter_input(INPUT_POST, 'FirstName', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $password = $_POST["password"];

    if (!$email) {
        $message = "Invalid email address.";
    } elseif (strlen($password) < 8) {
        $message = "Password must be at least 8 characters.";
    } else {
        $password = password_hash($password, PASSWORD_DEFAULT); // Secure password hashing
        $verificationToken = bin2hex(random_bytes(16)); // Generate a random token

        // Check if email already exists
        $stmt = $conn->prepare("SELECT id FROM merapyareusers WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        
        if ($stmt->num_rows > 0) {
            $message = "Email Address Already Exists!";
        } else {
            // Insert user into database with email_verified set to FALSE
            $stmt = $conn->prepare("INSERT INTO merapyareusers (FirstName, email, password, email_verification_token) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $firstName, $email, $password, $verificationToken);

            if ($stmt->execute()) {
                // Send verification email
                $mail = new PHPMailer(true);
                try {
                    // Server settings
                    $mail->isSMTP();
                    $mail->Host = 'smtp.hostinger.com';
                    $mail->SMTPAuth = true;
                    $mail->Username = 'no.reply@shopsager.in';
                    $mail->Password = '0Qg$xJhBF[7b'; // Replace with your actual SMTP password
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port = 587;

                    // Recipients
                    $mail->setFrom('no.reply@shopsager.in', 'ShopSAGE');
                    $mail->addAddress($email, $firstName);

                    // Content
                    $verificationLink = "https://shopsager.in/verify.php?email=" . urlencode($email) . "&token=" . urlencode($verificationToken);
                    $mail->isHTML(true);
                    $mail->Subject = 'Email Verification for ShopSAGE';
                $mail->Body = '
                <!DOCTYPE html>
                <html lang="en">
                <head>
                    <meta charset="UTF-8">
                    <meta name="viewport" content="width=device-width, initial-scale=1.0">
                    <style>
                        body {
                            font-family: Arial, sans-serif;
                            background-color: #f5f5f5;
                            margin: 0;
                            padding: 0;
                            color: #333;
                        }
                        .container {
                            width: 100%;
                            max-width: 600px;
                            margin: 0 auto;
                            background-color: #ffffff;
                            border-radius: 10px;
                            overflow: hidden;
                            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
                        }
                        .header {
                            background-color: #ff8c00;
                            padding: 20px;
                            text-align: center;
                        }
                        .header h1 {
                            color: #ffffff;
                            margin: 0;
                            font-size: 24px;
                        }
                        .content {
                            padding: 30px;
                        }
                        .content h2 {
                            color: #ff8c00;
                            font-size: 22px;
                            margin-bottom: 20px;
                        }
                        .content p {
                            font-size: 16px;
                            line-height: 1.6;
                        }
                        .content a {
                            display: inline-block;
                            margin-top: 20px;
                            padding: 10px 20px;
                            background-color: #ff8c00;
                            color: #ffffff;
                            text-decoration: none;
                            border-radius: 5px;
                            font-weight: bold;
                        }
                        .footer {
                            background-color: #333333;
                            color: #ffffff;
                            padding: 10px;
                            text-align: center;
                            font-size: 14px;
                        }
                    </style>
                </head>
                <body>
                    <div class="container">
                        <div class="header">
                            <h1>Welcome to ShopSAGE!</h1>
                        </div>
                        <div class="content">
                            <h2>Email Verification</h2>
                            <p>Hi ' . $firstName . ',</p>
                            <p>Thanks for signing up at ShopSAGE! To complete your registration, just click the button below to verify your email address.</p>
                            <a href="' . $verificationLink . '">Verify Email</a>
                            <p>If you did not create an account, please disregard this Email.</p>
                        </div>
                        <div class="footer">
                            <p>&copy; ' . date("Y") . ' ShopSAGE. All rights reserved.</p>
                            <p>Need help? <a href="mailto:support@shopsage.com">Contact Support</a></p>

                        </div>
                    </div>
                </body>
                </html>
                ';
                
                
                
                $mail->send();
                $message = "A verification email has been sent to your email address. Please check your inbox.";
            } catch (Exception $e) {
                $message = "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
            }
        } else {
            $message = "Error: " . $conn->error;
        }
    }
}
}


// Sign-In form processing
// Sign-In form processing
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['signIn'])) {
    // Validate CSRF token
    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        die("CSRF validation failed.");
    }

    // Input sanitization and validation
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $password = $_POST['password'];

    if (!$email) {
        $message = "Invalid email address.";
    } else {
        // Validate user credentials
        $stmt = $conn->prepare("SELECT id, password, email_verified FROM merapyareusers WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->bind_result($userId, $hashedPassword, $emailVerified);
        $stmt->fetch();

        if (password_verify($password, $hashedPassword)) {
            if ($emailVerified) {
                // Email is verified
                $_SESSION['user_id'] = $userId;
                $_SESSION['authenticated'] = true;
                header("Location: dasboard.php");
                exit();
            } else {
                $message = "Please verify your email before logging in.";
            }
        } else {
            $message = "Invalid email or password.";
        }
    }
}

// Check for logout message
if (isset($_GET['logged_out'])) {
    $message = "You have been logged out successfully.";
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <script src="https://kit.fontawesome.com/64d58efce2.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="style.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <title>Sign in & Sign up </title>
</head>
<body>
    <div class="container">
        <div class="forms-container">
            <div class="signin-signup">
                <!-- Sign-In Form -->
                <form action="#" method="post" class="sign-in-form">
                    <h2 class="title">Log In</h2>
                    <?php if (!empty($message)): ?>
                        <div class="alert alert-info">
                        <?php echo htmlspecialchars($message); ?>
                        </div>
                    <?php endif; ?>
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">

                    <div class="input-field">
                        <i class="fas fa-user"></i>
                        <input type="email" name="email" id="email" placeholder="Email" required />
                    </div>
                    <div class="input-field">
                        <i class="fas fa-lock"></i>
                        <input type="password" name="password" id="Password" placeholder="Password" required />
                    </div>
                    <a href="request_reset.php">Forget Your Password?</a>
                    <input type="submit" value="Log In" name="signIn" class="btn solid" />
                    <p class="social-text">Or Sign in with social platforms</p>
                    <div class="social-media">
                        <a href="#" class="social-icon">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="#" class="social-icon">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="#" class="social-icon">
                            <i class="fab fa-google"></i>
                        </a>
                        <a href="#" class="social-icon">
                            <i class="fab fa-linkedin-in"></i>
                        </a>
                    </div>
                </form>

                <!-- Sign-Up Form -->
                <form action="#" method="post" class="sign-up-form">
                    <h2 class="title">Create Account</h2>
                    <?php if (!empty($message)): ?>
                        <div class="alert alert-info">
                        <?php echo htmlspecialchars($message); ?>
                        </div>
                    <?php endif; ?>
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">

                    <div class="input-field">
                        <i class="fas fa-user"></i>
                        <input type="text" name="FirstName" id="FirstName" placeholder="Name" required />
                    </div>
                    <div class="input-field">
                        <i class="fas fa-envelope"></i>
                        <input type="email" name="email" id="email" placeholder="Email" required />
                    </div>
                    <div class="input-field">
                        <i class="fas fa-lock"></i>
                        <input type="password" name="password" id="password" placeholder="Password" required />
                    </div>
                    <input type="submit" class="btn" value="Sign up" name="signUp" />
                    <p class="social-text">Or Sign up with social platforms</p>
                    <div class="social-media">
                        <a href="#" class="social-icon">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="#" class="social-icon">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="#" class="social-icon">
                            <i class="fab fa-google"></i>
                        </a>
                        <a href="#" class="social-icon">
                            <i class="fab fa-linkedin-in"></i>
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <div class="panels-container">
            <div class="panel left-panel">
                <div class="content">
                    <h3>Hello, Buddy!</h3>
                    <p>
                        Register with your personal details to use all of site features.
                    </p>
                    <button class="btn transparent" id="sign-up-btn">
                        Sign up
                    </button>
                </div>
                <img src="log.svg" class="image" alt="" />
            </div>
            <div class="panel right-panel">
                <div class="content">
                    <h3>Welcome Back!</h3>
                    <p>
                        Enter your personal details to use all of site features.
                    </p>
                    <button class="btn transparent" id="sign-in-btn">
                        Log In
                    </button>
                </div>
                <img src="register.svg" class="image" alt="" />
            </div>
        </div>
    </div>

    <script src="app.js"></script>
    <script src="login.js"></script>
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7HUiX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>

</body>
</html>