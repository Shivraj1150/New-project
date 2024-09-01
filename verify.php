<?php
include 'partials/_dbconnect.php'; // Database connection

$message = ''; // Variable to store message
$is_success = false;

if (isset($_GET['email']) && isset($_GET['token'])) {
    $email = $_GET['email'];
    $token = $_GET['token'];

    // Verify the token and email
    $stmt = $conn->prepare("SELECT id FROM merapyareusers WHERE email = ? AND email_verification_token = ?");
    $stmt->bind_param("ss", $email, $token);
    $stmt->execute();
    $stmt->store_result();
    
    if ($stmt->num_rows > 0) {
        // Update user to set email_verified to TRUE and clear the token
        $stmt = $conn->prepare("UPDATE merapyareusers SET email_verified = TRUE, email_verification_token = NULL WHERE email = ?");
        $stmt->bind_param("s", $email);
        if ($stmt->execute()) {
            $message = "Your Email Has Been Verified Successfully. You Can Now Log In.";
            $is_success = true;
        } else {
            $message = "Error updating record: " . $conn->error;
        }
    } else {
        $message = "Invalid Verification Link or Email Is Already Verified.";
    }
} else {
    $message = "Invalid request.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Verification</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap');

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(to right, #ff7f50, #ff6347);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            overflow: hidden;
            color: #333;
        }
        .container {
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 8px 16px rgba(0,0,0,0.3);
            padding: 2rem;
            max-width: 500px;
            text-align: center;
            transform: translateY(100px);
            animation: slideUp 0.6s ease-out forwards;
        }
        .container h1 {
            font-size: 2rem;
            color: #ff6347;
            margin-bottom: 1rem;
            opacity: 0;
            animation: fadeIn 1s 0.4s ease-out forwards;
        }
        .container p {
            font-size: 1rem;
            margin-bottom: 1.5rem;
            opacity: 0;
            animation: fadeIn 1s 0.6s ease-out forwards;
        }
        .container .btn {
            display: inline-block;
            padding: 0.8rem 1.8rem;
            font-size: 1rem;
            color: #fff;
            background-color: #ff7f50;
            border: none;
            border-radius: 50px;
            text-decoration: none;
            text-transform: uppercase;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.3s ease;
            opacity: 0;
            animation: fadeIn 1s 0.8s ease-out forwards;
        }
        .container .btn:hover {
            background-color: #ff6347;
            transform: translateY(-2px);
        }
        .container .error {
            color: #e74c3c;
        }
        .container .success {
            color: #2ecc71;
        }
        @keyframes slideUp {
            to {
                transform: translateY(0);
            }
        }
        @keyframes fadeIn {
            to {
                opacity: 1;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Email Verification</h1>
        <p class="<?php echo $is_success ? 'success' : 'error'; ?>">
            <?php echo $message; ?>
        </p>
        <a href="_login.php" class="btn">Go to Login</a>
    </div>
</body>
</html>
