<?php
session_start();
include 'partials/_dbconnect.php'; // Include your database connection file

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'User not logged in']);
    exit();
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = $_POST['product_id'];
    $product_name = $_POST['product_name'];
    $product_price = $_POST['product_price'];
    $product_image = $_POST['product_image'];
    $quantity = $_POST['quantity'];
    $size = $_POST['size']; // Get the size
    $color = $_POST['color']; // Get the color

    // Check if the product with the same size and color is already in the user's cart
    $query = "SELECT * FROM cart WHERE user_id = ? AND product_id = ? AND size = ? AND color = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("isss", $user_id, $product_id, $size, $color);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo json_encode(['status' => 'error', 'message' => 'Product with selected size and color is already in cart']);
    } else {
        // Insert new product with size and color into cart
        $query = "INSERT INTO cart (user_id, product_id, product_name, product_price, product_image, quantity, size, color) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("issdsiss", $user_id, $product_id, $product_name, $product_price, $product_image, $quantity, $size, $color);

        if ($stmt->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'Product added to cart']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to add product to cart']);
        }
    }
}

// Retrieve cart items for the logged-in user
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $query = "SELECT * FROM cart WHERE user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $cart_items = [];

    while ($row = $result->fetch_assoc()) {
        $cart_items[] = $row;
    }

    echo json_encode($cart_items);
}

if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    parse_str(file_get_contents("php://input"), $_PUT);
    $product_id = $_PUT['product_id'];
    $quantity = $_PUT['quantity'];
    $size = $_PUT['size'];  // Retrieve size from request
    $color = $_PUT['color']; // Retrieve color from request

    // Update the quantity in the cart
    $query = "UPDATE cart SET quantity = ? WHERE user_id = ? AND product_id = ? AND size = ? AND color = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("iisss", $quantity, $user_id, $product_id, $size, $color);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to update quantity']);
    }
}

// Remove item from cart
if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    parse_str(file_get_contents("php://input"), $_DELETE);
    $product_id = $_DELETE['product_id'];
    $size = $_DELETE['size'];  // Retrieve size from request
    $color = $_DELETE['color']; // Retrieve color from request

    $query = "DELETE FROM cart WHERE user_id = ? AND product_id = ? AND size = ? AND color = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("isss", $user_id, $product_id, $size, $color);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Product removed from cart']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to remove product from cart']);
    }
}
?>
