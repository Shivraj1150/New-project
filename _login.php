
<?php 

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// require 'vendor/autoload.php'; // Include Composer autoload

session_start();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;
require 'PHPMailer-master/src/PHPMailer.php';
require 'PHPMailer-master/src/Exception.php';
require 'PHPMailer-master/src/SMTP.php';


function sendMail($email,$verificationToken){
    require 'PHPMailer/Exception.php';
    require 'PHPMailer/PHPMailer.php';
    require 'PHPMailer/SMTP.php';

    $mail = new PHPMailer(true);

}

include 'partials/_dbconnect.php'; // Database connection

if (isset($_POST['signUp'])) {
    $firstName = $_POST["FirstName"];
    $email = $_POST["email"];
    $password = password_hash($_POST["password"], PASSWORD_DEFAULT);
    $verificationToken = bin2hex(random_bytes(16)); // Generate a random token

    // Check if email already exists
    $stmt = $conn->prepare("SELECT id FROM Users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    
    if ($stmt->num_rows > 0) {
        echo "Email Address Already Exists!";
    } else {
        // Insert user into database with email_verified set to FALSE
        $stmt = $conn->prepare("INSERT INTO Users (FirstName, email, password, email_verification_token) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $firstName, $email, $password, $verificationToken);

        if ($stmt->execute()) {
            // Send verification email
            $mail = new PHPMailer(true);
            try {
                // Server settings
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com'; // Set your SMTP server
                $mail->SMTPAuth = true;
                $mail->Username = 'no.reply.storesage@gmail.com'; // SMTP username
                $mail->Password = 'mbqd zcqj mnei ybis'; // SMTP password
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;

                // Recipients
                $mail->setFrom('no.reply.storesage@gmail.com', 'ShopSAGE');
                $mail->addAddress($email, $firstName);

                // Content
                $verificationLink = "http://localhost/New-project/verify.php?email=" . urlencode($email) . "&token=" . urlencode($verificationToken);
                $mail->isHTML(true);
                $mail->Subject = 'Email Verification';
                $mail->Body    = "Hi $firstName,<br><br>Please click the link below to verify your email address:<br><a href='$verificationLink'>$verificationLink</a><br><br>Thanks!";

                $mail->send();
                echo "A verification email has been sent to your email address. Please check your inbox.";
            } catch (Exception $e) {
                echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
            }
        } else {
            echo "Error: " . $conn->error;
        }
    }
}



if (isset($_POST['signIn'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Validate user credentials
    $stmt = $conn->prepare("SELECT id, password, email_verified FROM Users WHERE email = ?");
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
            echo "Please verify your email before logging in.";
        }
    } else {
        echo "Invalid email or password.";
    }
}
?>

<?php

// Check for logout message
$logoutMessage = "";
if (isset($_GET['logged_out'])) {
    $logoutMessage = "You have been logged out successfully.";
}

// Add your existing login handling code here...
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="login.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <title>Modern Login Page | AsmrProg</title>
</head>

<body>

<?php if ($logoutMessage) : ?>
    <p><?php echo $logoutMessage; ?></p>
<?php endif; ?>

    <div class="container" id="container">
        <div class="form-container sign-up">
            <form  method="post" action="">
                <h1>Create Account</h1>
                <div class="social-icons">
                    <a href="#" class="icon"><i class="fa-brands fa-google-plus-g"></i></a>
                    <a href="#" class="icon"><i class="fa-brands fa-facebook-f"></i></a>
                    <a href="#" class="icon"><i class="fa-brands fa-github"></i></a>
                    <a href="#" class="icon"><i class="fa-brands fa-linkedin-in"></i></a>
                </div>
                <span>or use your email for registeration</span>
                <input type="text" name="FirstName" id="FirstName" placeholder="Name">
                <input type="email" name="email" id="email" placeholder="Email">
                <input type="password" name="password" id="password" placeholder="Password">
                <button name="signUp">Sign Up</button>
               

            </form>
        </div>
        <div class="form-container sign-in">
            <form method="post" action="">
                <h1>Login In</h1>
                <div class="social-icons">
                    <a href="#" class="icon"><i class="fa-brands fa-google-plus-g"></i></a>
                    <a href="#" class="icon"><i class="fa-brands fa-facebook-f"></i></a>
                    <a href="#" class="icon"><i class="fa-brands fa-github"></i></a>
                    <a href="#" class="icon"><i class="fa-brands fa-linkedin-in"></i></a>
                </div>
                <span>or use your email password</span>
                <input type="email" name="email" id="email" placeholder="Email">
                <input type="password" name="password" id="Password" placeholder="Password">
                <a href="#">Forget Your Password?</a>
                <button name="signIn">Login In</button>

            </form>
        </div>
        <div class="toggle-container">
            <div class="toggle">
                <div class="toggle-panel toggle-left">
                    <h1>Welcome Back!</h1>
                    <p>Enter your personal details to use all of site features</p>
                    <button class="hidden" id="login">Login In</button>
                </div>
                <div class="toggle-panel toggle-right">
                    <h1>Hello, Friend!</h1>
                    <p>Register with your personal details to use all of site features</p>
                    <button class="hidden" id="register">Sign Up</button>
                </div>
            </div>
        </div>
    </div>

    <script src="login.js"></script>
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>

</body>

</html>
