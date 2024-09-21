<?php
header('Content-Type: application/json');

$server = "localhost";
$username = "root";
$password = "";
$database = "merapyareusers";

$conn = mysqli_connect($server, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$productId = intval($_GET['id']);
$sql = "SELECT * FROM product_details WHERE id = $productId";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $product = $result->fetch_assoc();

    // Check if 'sizes' is stored as JSON or a comma-separated string
    if (!empty($product['sizes'])) {
        // If it's a JSON string, decode it. If it's comma-separated, split it into an array
        if (strpos($product['sizes'], '[') === 0) {
            // JSON format: decode to array
            $product['sizes'] = json_decode($product['sizes']);
        } else {
            // Comma-separated string: explode to array
            $product['sizes'] = explode(',', $product['sizes']);
        }
    } else {
        $product['sizes'] = [];
    }
    if (!empty($product['colors'])) {
        // If it's a JSON string, decode it. If it's comma-separated, split it into an array
        if (strpos($product['colors'], '[') === 0) {
            // JSON format: decode to array
            $product['colors'] = json_decode($product['colors']);
        } else {
            // Comma-separated string: explode to array
            $product['colors'] = explode(',', $product['colors']);
        }
    } else {
        $product['colors'] = [];
    }

    echo json_encode($product);
} else {
    echo json_encode([]);
}

$conn->close();
?>
