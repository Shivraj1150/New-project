<?php
// Security Headers
header("Strict-Transport-Security: max-age=63072000; includeSubDomains; preload"); // HSTS
header("Content-Security-Policy: default-src 'self'; style-src 'self' 'https://cdnjs.cloudflare.com' 'unsafe-inline'; font-src 'self' 'https://cdnjs.cloudflare.com'; script-src 'self';");
header("X-Content-Type-Options: nosniff"); // Prevent MIME type sniffing
header("X-XSS-Protection: 1; mode=block"); // XSS protection
header("X-Frame-Options: DENY"); // Prevent clickjacking
header("Referrer-Policy: no-referrer"); // Control referrer information

if (isset($_GET['token'])) {
    // Sanitize and validate token
    $token = htmlspecialchars($_GET['token'], ENT_QUOTES, 'UTF-8');

    // Check if the token is a valid hex string (128-bit token should be 64 characters)
    if (!preg_match('/^[a-f0-9]{64}$/', $token)) {
        die('Invalid token format.');
    }
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Reset Password</title>
        <!-- Google Fonts -->
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
        <style>
            /* Styling */
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
            }
            body {
                font-family: 'Poppins', sans-serif;
                background-color: #f7f8fc;
                display: flex;
                justify-content: center;
                align-items: center;
                height: 100vh;
                margin: 0;
                overflow: hidden;
            }
            .container {
                background-color: white;
                padding: 2.5rem;
                border-radius: 12px;
                box-shadow: 0 12px 24px rgba(0, 0, 0, 0.1);
                max-width: 400px;
                width: 100%;
                animation: fadeIn 1.2s ease-in-out;
            }
            h2 {
                margin-bottom: 2rem;
                font-weight: 600;
                text-align: center;
                color: #333;
            }
            label {
                display: block;
                margin-bottom: 0.5rem;
                font-weight: 400;
                color: #666;
            }
            input[type="password"] {
                width: 100%;
                padding: 0.85rem;
                margin-bottom: 1.5rem;
                border: 1px solid #ddd;
                border-radius: 8px;
                font-size: 1rem;
                transition: border-color 0.3s ease;
            }
            input[type="password"]:focus {
                border-color: #ff7f50;
                outline: none;
            }
            button {
                width: 100%;
                padding: 0.85rem;
                background-color: #ff7f50;
                color: white;
                border: none;
                border-radius: 8px;
                cursor: pointer;
                font-size: 1rem;
                transition: background-color 0.3s ease, transform 0.2s ease;
            }
            button:hover {
                background-color: #e67340;
                transform: translateY(-3px);
            }
            .password-toggle {
                cursor: pointer;
                font-size: 0.9rem;
                margin-top: -1rem;
                margin-bottom: 1.5rem;
                display: inline-block;
                color: #ff7f50;
                transition: color 0.3s ease;
            }
            .password-toggle:hover {
                color: #e67340;
            }
            @keyframes fadeIn {
                from {
                    opacity: 0;
                    transform: translateY(20px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }
        </style>
    </head>
    <body>
        <div class="container">
            <h2>Reset Your Password</h2>
            <form action="reset_password.php?token=<?php echo $token; ?>" method="POST" onsubmit="return validatePassword()">
                <label for="password">New Password:</label>
                <input type="password" id="password" name="password" required>
                <span class="password-toggle" onclick="togglePassword()">Show Password</span>
                <button type="submit">Reset Password</button>
            </form>
        </div>

        <script>
            function togglePassword() {
                const passwordField = document.getElementById('password');
                const toggleText = document.querySelector('.password-toggle');
                if (passwordField.type === 'password') {
                    passwordField.type = 'text';
                    toggleText.textContent = 'Hide Password';
                } else {
                    passwordField.type = 'password';
                    toggleText.textContent = 'Show Password';
                }
            }

            function validatePassword() {
                const password = document.getElementById('password').value;
                const passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&#])[A-Za-z\d@$!%*?&#]{8,}$/;
                if (!passwordRegex.test(password)) {
                    alert('Password must be at least 8 characters long, contain one uppercase letter, one lowercase letter, one number, and one special character.');
                    return false;
                }
                return true;
            }
        </script>
    </body>
    </html>
    <?php
} else {
    // Security response for no token provided
    echo '<h3>No token provided. Access denied.</h3>';
}
?>
