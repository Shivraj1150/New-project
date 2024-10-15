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

// Directory where you want to store the uploaded images
$target_dir = "uploads/";

// Function to upload an image
function upload_image($image_field, $target_dir) {
    if (isset($_FILES[$image_field]) && $_FILES[$image_field]['error'] == 0) {
        $target_file = $target_dir . basename($_FILES[$image_field]['name']);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Check if file is an actual image
        $check = getimagesize($_FILES[$image_field]["tmp_name"]);
        if ($check === false) {
            die("File is not an image.");
        }

        // Allow only certain file formats
        $allowed_types = array("jpg", "jpeg", "png", "gif");
        if (!in_array($imageFileType, $allowed_types)) {
            die("Only JPG, JPEG, PNG, and GIF files are allowed.");
        }

        // Move the uploaded file to the target directory
        if (!move_uploaded_file($_FILES[$image_field]["tmp_name"], $target_file)) {
            die("Sorry, there was an error uploading your file.");
        }

        return $target_file; // Return the file path to store in the database
    }

    return null; // No file uploaded
}

// Function to upload a video// Function to upload a video with added debugging
function upload_video($video_field, $target_dir) {
    // Check if the video file was uploaded
    if (isset($_FILES[$video_field]) && $_FILES[$video_field]['error'] == 0) {
        // Debugging: Output the file upload status
        echo "<pre>";
        print_r($_FILES[$video_field]);
        echo "</pre>";

        $target_file = $target_dir . basename($_FILES[$video_field]['name']);
        $videoFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Check if the video is in an allowed format
        $allowed_types = array("mp4", "avi", "mov", "wmv");
        if (!in_array($videoFileType, $allowed_types)) {
            die("Only MP4, AVI, MOV, and WMV files are allowed.");
        }

        // Attempt to move the uploaded file to the target directory
        if (move_uploaded_file($_FILES[$video_field]["tmp_name"], $target_file)) {
            echo "The video file " . htmlspecialchars(basename($_FILES[$video_field]["name"])) . " has been uploaded.";
            return $target_file; // Return the file path to store in the database
        } else {
            die("Sorry, there was an error uploading your video.");
        }
    } else {
        // Debugging: Output error message if the file wasn't uploaded
        echo "Error uploading video: " . $_FILES[$video_field]['error'];
    }

    return null; // No video uploaded
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
    if (isset($_FILES['video'])) {
        echo "<pre>";
        print_r($_FILES['video']); // This will output file details to the browser
        echo "</pre>";
    }

    // Upload images and get their file paths
    $image_1 = upload_image('image_1', $target_dir);
    $image_2 = upload_image('image_2', $target_dir);
    $image_3 = upload_image('image_3', $target_dir);
    $image_4 = upload_image('image_4', $target_dir);

    // Handle sizes and colors input
    $sizes = isset($_POST['sizes']) ? $_POST['sizes'] : '';
    $sizes = array_map('trim', explode(',', $sizes));
    $sizes = json_encode($sizes);
    $video_file = upload_video('video', $target_dir); // This line was missing!

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
        $image_1, // Insert the file paths instead of URLs
        $image_2,
        $image_3,
        $image_4,
        $video_file,
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
    <form action="admin.php" method="POST" enctype="multipart/form-data">
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

        <!-- File inputs for images -->
        <label for="image_1">Primary Product Image:</label>
        <input type="file" name="image_1" required>

        <label for="image_2">Secondary Product Image (Optional):</label>
        <input type="file" name="image_2">

        <label for="image_3">Third Product Image (Optional):</label>
        <input type="file" name="image_3">

        <label for="image_4">Fourth Product Image (Optional):</label>
        <input type="file" name="image_4">

        <label for="video">Product Video URL (Optional):</label>
        <input type="file" name="video" accept="video/*">


        <label for="category">Select Category:</label>
        <select name="category" required>
            <option value="shoes">Shoes</option>
            <option value="menwatches">Men Watches</option>
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
            <option value="sunglasses">sunglasses</option>
            <option value="perfumes">perfumes</option>
            <option value="hoddies">Women Hoddies</option>
            <option value="joggers">Women joggers</option>
            <option value="shirts">Women shirts</option>
            <option value="shirts">Women tshirts</option>
            <option value="womenwatches">Women watches</option>
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
