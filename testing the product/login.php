<?php
session_start();

// Set a limit on login attempts (e.g., 5 attempts within 10 minutes)
$max_attempts = 5;
$lockout_time = 10 * 60; // 10 minutes

if (!isset($_SESSION['attempts'])) {
    $_SESSION['attempts'] = 0;
    $_SESSION['last_attempt_time'] = time();
}

// Check if the user is locked out
if ($_SESSION['attempts'] >= $max_attempts && (time() - $_SESSION['last_attempt_time']) < $lockout_time) {
    die("Too many login attempts. Please try again after 10 minutes.");
}

// Use a hashed password for better security (replace with your hashed password)
$stored_username = 'admin';
$stored_password_hash = password_hash('mypassword', PASSWORD_DEFAULT); // Replace with the actual hashed password

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
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
        <button type="submit">Login</button>
    </form>
</body>
</html>
