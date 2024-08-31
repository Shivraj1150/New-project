<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
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
