<?php
// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

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

// Sign-Up form processing
if (isset($_POST['signUp'])) {
    $firstName = $_POST["FirstName"];
    $email = $_POST["email"];
    $password = password_hash($_POST["password"], PASSWORD_DEFAULT);
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
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'no.reply.storesage@gmail.com';
                $mail->Password = 'mbqd zcqj mnei ybis'; // Replace with your actual SMTP password
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;

                // Recipients
                $mail->setFrom('no.reply.storesage@gmail.com', 'ShopSAGE');
                $mail->addAddress($email, $firstName);

                // Content
                $verificationLink = "http://localhost/New-project/verify.php?email=" . urlencode($email) . "&token=" . urlencode($verificationToken);
                $mail->isHTML(true);
                $mail->Subject = 'Please Verify Your Email for ShopSAGE';
$mail->Body    = "
<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <style>
        body {
            font-family: 'Helvetica Neue', Arial, sans-serif;
            background-color: #f8f9fa;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 100%;
            max-width: 600px;
            margin: 40px auto;
            padding: 20px;
            background-color: #ffffff;
            border-radius: 12px;
            border: 1px solid #ddd;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header img {
            max-width: 120px;
        }
        h1 {
            color: #ff6600;
            font-size: 24px;
            margin: 0;
            padding: 0;
        }
        p {
            line-height: 1.6;
            margin: 0 0 20px;
        }
        .button {
            display: inline-block;
            padding: 12px 24px;
            font-size: 16px;
            color: #ffffff;
            background-color: #ff6600;
            text-decoration: none;
            border-radius: 6px;
            text-align: center;
            font-weight: bold;
            margin: 20px 0;
        }
        .footer {
            text-align: center;
            font-size: 14px;
            color: #777;
            margin-top: 30px;
        }
        .footer a {
            color: #ff6600;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class='container'>
        <div class='header'>
            <img src='https://via.placeholder.com/120x50?text=ShopSAGE' alt='ShopSAGE Logo'>
        </div>
        <h1>Hello, $firstName!</h1>
        <p>Welcome to ShopSAGE! To complete your registration, we need to verify your email address.</p>
        <p>Please click the button below to verify your email:</p>
        <a href='$verificationLink' class='button'>Verify Your Email</a>
        <p>If you didn’t request this, please ignore this email.</p>
        <div class='footer'>
            <p>&copy; " . date('Y') . " ShopSAGE. All rights reserved.</p>
            <p><a href='#'>Unsubscribe</a> | <a href='#'>Contact Us</a></p>
        </div>
    </div>
</body>
</html>
";
                
                
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

// Sign-In form processing
if (isset($_POST['signIn'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

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
            header("Location: welcome.php");
            exit();
        } else {
            $message = "Please verify your email before logging in.";
        }
    } else {
        $message = "Invalid email or password.";
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
                            <?php echo $message; ?>
                        </div>
                    <?php endif; ?>
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
                            <?php echo $message; ?>
                        </div>
                    <?php endif; ?>
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