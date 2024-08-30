<?php
include 'partials/_dbconnect.php'; // Database connection

if (isset($_GET['email']) && isset($_GET['token'])) {
    $email = $_GET['email'];
    $token = $_GET['token'];

    // Verify the token and email
    $stmt = $conn->prepare("SELECT id FROM Users WHERE email = ? AND email_verification_token = ?");
    $stmt->bind_param("ss", $email, $token);
    $stmt->execute();
    $stmt->store_result();
    
    if ($stmt->num_rows > 0) {
        // Update user to set email_verified to TRUE and clear the token
        $stmt = $conn->prepare("UPDATE Users SET email_verified = TRUE, email_verification_token = NULL WHERE email = ?");
        $stmt->bind_param("s", $email);
        if ($stmt->execute()) {
            echo "Your email has been verified successfully. You can now log in.";
        } else {
            echo "Error updating record: " . $conn->error;
        }
    } else {
        echo "Invalid verification link or email already verified.";
    }
} else {
    echo "Invalid request.";
}
?>
