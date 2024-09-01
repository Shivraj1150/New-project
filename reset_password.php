<?php
session_start();
include 'partials/_dbconnect.php';

$resetSuccess = false;
$errorMessage = '';

if (isset($_GET['token'])) {
    $token = $_GET['token'];

    // Check if the token exists in the password_resets table
    $stmt = $conn->prepare("SELECT email FROM password_resets WHERE token = ?");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($email);
        $stmt->fetch();

        if (isset($_POST['password'])) {
            $new_password = password_hash($_POST['password'], PASSWORD_DEFAULT);

            // Update the user's password in the users table
            $stmt = $conn->prepare("UPDATE merapyareusers SET password = ? WHERE email = ?");
            $stmt->bind_param("ss", $new_password, $email);
            $stmt->execute();

            // Remove the token from the password_resets table
            $stmt = $conn->prepare("DELETE FROM password_resets WHERE token = ?");
            $stmt->bind_param("s", $token);
            $stmt->execute();

            $resetSuccess = true;
        }
    } else {
        $errorMessage = 'Invalid or expired token.';
    }
} else {
    $errorMessage = 'No token provided.';
}
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
            background: #fff5e6;
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
            <p class="error-message"><?php echo $errorMessage; ?></p>
        <?php endif; ?>
        <a href="_login.php" class="btn"><i class="fas fa-sign-in-alt"></i> Back to Login</a>
    </div>
</body>
</html>
