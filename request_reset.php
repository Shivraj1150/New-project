<?php
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
            $mail->setFrom('no.reply.storesage@gmail.com', 'Your Name');
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

</head>
<body>
    
</body>
</html>