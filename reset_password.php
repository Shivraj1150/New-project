<?php
session_start();
include 'partials/_dbconnect.php';

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

            echo 'Your password has been updated.';
        }
    } else {
        echo 'Invalid or expired token.';
    }
} else {
    echo 'No token provided.';
}
?>
