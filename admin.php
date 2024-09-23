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
include 'partials/_dbconnect.php';

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // CSRF token validation
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        die("Invalid CSRF token");
    }

    // Check if the form has already been processed
    if (isset($_SESSION['form_submitted']) && $_SESSION['form_submitted'] === true) {
        echo "The form has already been submitted.";
        exit;
    }

    // Get image URLs from the form
    $image_1 = $_POST['image_url_1'] ?? null;
    $image_2 = $_POST['image_url_2'] ?? null;
    $image_3 = $_POST['image_url_3'] ?? null;
    $image_4 = $_POST['image_url_4'] ?? null;

    // Handle sizes input
    $sizes = isset($_POST['sizes']) ? $_POST['sizes'] : '';
    $sizes = array_map('trim', explode(',', $sizes));
    $sizes = json_encode($sizes);

    // Handle colors input
    $colors = isset($_POST['colors']) ? $_POST['colors'] : '';
    $colors = array_map('trim', explode(',', $colors));
    $colors = json_encode($colors);

    // Insert product data into the database
    $stmt = $conn->prepare("INSERT INTO product_details (name, price, description, image_url_1, image_url_2, image_url_3, image_url_4, video_url, category, sizes, colors) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param(
        "sdsssssssss",
        $_POST['name'],
        $_POST['price'],
        $_POST['description'],
        $image_1,
        $image_2,
        $image_3,
        $image_4,
         // Placeholder for video URL, if not used
        $video,
   
        $_POST['category'],
        $sizes,
        $colors
    );

    if ($stmt->execute()) {
        // Set session variable to indicate successful form submission
        $_SESSION['form_submitted'] = true;
        // Redirect to the same page to avoid resubmission
        header('Location: admin.php');
        exit;
    } else {
        echo "Error: " . $stmt->error;
    }
}

// Unset form submission flag on page load
unset($_SESSION['form_submitted']);
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
            background-color: #F4F7F6;
            color: #333;
            padding: 20px;
        }
        h1, h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #FF6B35;
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
            border-color: #FF6B35;
            outline: none;
            box-shadow: 0 0 5px rgba(255, 107, 53, 0.5);
        }
        form button {
            display: block;
            width: 100%;
            padding: 15px;
            background-color: #FF6B35;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
            transition: 0.3s;
        }
        form button:hover {
            background-color: #E8602C;
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
            color: #FF6B35;
        }
        .product-card p {
            margin: 5px 0;
        }
    </style>
</head>
<body>
    <form action="logout.php" method="POST">
        <button type="submit">Logout</button>
    </form>

    <h1>Add a New Product</h1>
    <form action="admin.php" method="POST">
        <label for="name">Product Name:</label>
        <input type="text" name="name" required>

        <label for="price">Price:</label>
        <input type="number" step="0.01" name="price" required>

        <label for="description">Description:</label>
        <textarea name="description" required></textarea>

        <label for="sizes">Available Sizes (comma-separated):</label>
        <input type="text" name="sizes" placeholder="e.g., S,M,L,XL">

        <label for="colors">Available Colors (comma-separated):</label>
        <input type="text" name="colors" placeholder="e.g., Blue, Black, Red">

        <label for="image_url_1">Primary Product Image URL:</label>
        <input type="text" name="image_url_1" required>

        <label for="image_url_2">Secondary Product Image URL (Optional):</label>
        <input type="text" name="image_url_2">

        <label for="image_url_3">Third Product Image URL (Optional):</label>
        <input type="text" name="image_url_3">

        <label for="image_url_4">Fourth Product Image URL (Optional):</label>
        <input type="text" name="image_url_4">

        <label for="video">Product Video URL (Optional):</label>
        <input type="text" name="video" placeholder="URL for the video">

        <label for="category">Select Category:</label>
        <select name="category" required>
            <option value="shoes">Shoes</option>
            <option value="watches">Men Watches</option>
            <option value="tshirt">Men T-Shirt</option>
            <option value="shirt">Men Shirt</option>
            <option value="joggers">Men Joggers</option>
            <option value="hoodies">Men Hoodies</option>
            <option value="handbags">Hand Bags</option>
            <option value="airpods">AirPods</option>
            <option value="headsets">Headsets</option>
            <option value="power_bank">Power Bank</option>
            <option value="home_decor">Home Decor</option>
            <option value="speakers">Speakers</option>
            <option value="speakers">sunglasses</option>
            <option value="speakers">perfumes</option>
            <option value="speakers">Women Hoddies</option>
            <option value="speakers">Women joggers</option>
            <option value="speakers">Women shirts</option>
            <option value="speakers">Women tshirts</option>
            <option value="speakers">Women watches</option>
        </select>

        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

        <button type="submit">Add Product</button>
    </form>

    <hr>

    <h2>Existing Products</h2>
    <?php
    // Fetch products from the database
    $result = $conn->query("SELECT * FROM product_details");

    if ($result) {
        while ($row = $result->fetch_assoc()) {
            // Decode the sizes JSON data
            $sizes = json_decode($row['sizes'], true);
            $sizes_list = is_array($sizes) ? implode(', ', $sizes) : 'Not specified';

            // Decode the colors JSON data
            $colors = json_decode($row['colors'], true);
            $colors_list = is_array($colors) ? implode(', ', $colors) : 'Not specified';

            echo '
            <div class="product-card">
                <img src="' . htmlspecialchars($row['image_url_1'], ENT_QUOTES, 'UTF-8') . '" alt="' . htmlspecialchars($row['name'], ENT_QUOTES, 'UTF-8') . '">
                <div>
                    <h3>' . htmlspecialchars($row['name'], ENT_QUOTES, 'UTF-8') . '</h3>
                    <p>' . htmlspecialchars($row['description'], ENT_QUOTES, 'UTF-8') . '</p>
                    <p>Price: ' . htmlspecialchars($row['price'], ENT_QUOTES, 'UTF-8') . 'rs</p>
                    <p>Category: ' . htmlspecialchars($row['category'], ENT_QUOTES, 'UTF-8') . '</p>
                    <p>Sizes: ' . htmlspecialchars($sizes_list, ENT_QUOTES, 'UTF-8') . '</p>
                    <p>Colors: ' . htmlspecialchars($colors_list, ENT_QUOTES, 'UTF-8') . '</p>
                </div>
            </div>';
        }
    } else {
        echo "Error fetching products: " . $conn->error;
    }

    $conn->close();
    ?>
</body>
</html>
