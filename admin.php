<?php
session_start();

// Check if the user is logged in, otherwise redirect to login page
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit;
}



// CSRF Token Generation
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Database connection
$conn = new mysqli('localhost', 'root', '', 'merapyareusers');

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // CSRF token validation
    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        die("Invalid CSRF token");
    }

    // File upload logic
    $image = $_FILES['image']['name'];
    $target = "uploads/" . basename($image);

    // Check file type before uploading
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
    $file_type = mime_content_type($_FILES['image']['tmp_name']);

    if (in_array($file_type, $allowed_types)) {
        if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
            // Insert product data into the database securely with prepared statements
            $name = $_POST['name'];
            $price = $_POST['price'];
            $description = $_POST['description'];
            $category = $_POST['category'];

            $stmt = $conn->prepare("INSERT INTO product_details (name, price, description, image_url_1, category) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sdsss", $name, $price, $description, $image, $category);

            if ($stmt->execute()) {
                echo "Product added successfully to the $category category!";
            } else {
                echo "Error: " . $stmt->error;
            }
        } else {
            echo "Failed to upload image.";
        }
    } else {
        echo "Invalid file type. Only JPEG, PNG, and GIF are allowed.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Add Products</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }
        body {
            background-color: #f4f7f6;
            color: #333;
            padding: 20px;
        }
        h1, h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #ff6b35;
        }
        form {
            background-color: white;
            max-width: 600px;
            margin: 20px auto;
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        form label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }
        form input, form textarea, form select {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
            transition: 0.3s;
        }
        form input:focus, form textarea:focus, form select:focus {
            border-color: #ff6b35;
            outline: none;
            box-shadow: 0 0 5px rgba(255, 107, 53, 0.5);
        }
        form button {
            display: block;
            width: 100%;
            padding: 15px;
            background-color: #ff6b35;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
            transition: 0.3s;
        }
        form button:hover {
            background-color: #e8602c;
            transform: scale(1.02);
        }
        hr {
            border: 0;
            height: 1px;
            background: #ddd;
            margin: 40px 0;
        }
        .product-card {
            background-color: white;
            padding: 20px;
            margin: 20px auto;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            display: flex;
            align-items: center;
        }
        .product-card img {
            width: 100px;
            margin-right: 20px;
            border-radius: 10px;
        }
        .product-card h3 {
            color: #ff6b35;
        }
        .product-card p {
            margin: 5px 0;
        }
    </style>
</head>
<body>
    <!-- Add this Logout button to admin.php -->
<form action="logout.php" method="POST">
    <button type="submit">Logout</button>
</form>

    <h1>Add a New Product</h1>
    <form action="admin.php" method="POST" enctype="multipart/form-data">
        <label for="name">Product Name:</label>
        <input type="text" name="name" required>

        <label for="price">Price:</label>
        <input type="number" step="0.01" name="price" required>

        <label for="description">Description:</label>
        <textarea name="description" required></textarea>

        <label for="image">Product Image:</label>
        <input type="file" name="image" required>

        <label for="category">Select Category:</label>
        <select name="category" required>
            <option value="shoes">Shoes</option>
            <option value="watches">Watches</option>
            <option value="perfumes">Perfumes</option>
            <option value="glasses">Glasses</option>
        </select>

        <!-- Include CSRF token -->
        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

        <button type="submit">Add Product</button>
    </form>

    <hr>

    <h2>Existing Products</h2>
    <?php
    // Fetch products from the database
    $result = $conn->query("SELECT * FROM product_details");

    while ($row = $result->fetch_assoc()) {
        echo '
        <div class="product-card">
            <img src="uploads/' . htmlspecialchars($row['image_url_1'], ENT_QUOTES, 'UTF-8') . '" alt="' . htmlspecialchars($row['name'], ENT_QUOTES, 'UTF-8') . '">
            <div>
                <h3>' . htmlspecialchars($row['name'], ENT_QUOTES, 'UTF-8') . '</h3>
                <p>' . htmlspecialchars($row['description'], ENT_QUOTES, 'UTF-8') . '</p>
                <p>Price: ' . htmlspecialchars($row['price'], ENT_QUOTES, 'UTF-8') . 'rs</p>
                <p>Category: ' . htmlspecialchars($row['category'], ENT_QUOTES, 'UTF-8') . '</p>
            </div>
        </div>';
    }

    $conn->close();
    ?>
</body>
</html>
