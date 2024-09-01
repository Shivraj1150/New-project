<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
include 'partials/_dbconnect.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require 'PHPMailer-master/src/PHPMailer.php';
require 'PHPMailer-master/src/Exception.php';
require 'PHPMailer-master/src/SMTP.php';

if (isset($_POST['email'])) {
    $email = $_POST['email'];
    

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
            $mail->Host = 'smtp.gmail.com';  // Set your SMTP server
            $mail->SMTPAuth = true;
            $mail->Username = 'no.reply.storesage@gmail.com';  // SMTP username
            $mail->Password = 'mbqd zcqj mnei ybis';  // SMTP password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            // Recipients
            $mail->setFrom('no.reply.storesage@gmail.com', 'ShopSAGE');
            $mail->addAddress($email);

            // Content
            $mail->isHTML(true);
            $mail->Subject = 'Password Reset Request';
            $mail->Body = "Please click on the following link to reset your password: 
            <a href='http://localhost/New-project/reset_password_form.php?token=$token'>Reset Password</a>";

            $mail->send();
            echo '<div class="alert alert-success" role="alert">
                    <strong>Success!</strong> A password reset link has been sent to your email. Please check your inbox.
                  </div>';
        } catch (Exception $e) {
            echo '<div class="alert alert-danger" role="alert">
                    <strong>Error!</strong> Message could not be sent. Mailer Error: ' . $mail->ErrorInfo . '
                  </div>';
        }
    } else {
        echo '<div class="alert alert-warning" role="alert">
                <strong>Warning!</strong> No account found with that email.
              </div>';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
            <label for="email">Enter your email address:</label>
            <input type="email" name="email" id="email" required>
            <button type="submit">Request Password Reset</button>
        </form>
    </div>
</body>
</html>