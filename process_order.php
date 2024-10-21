<?php
session_start(); // Start the session
// Include your database connection
include 'partials/_dbconnect.php';



// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_POST['user_id'];
    $shipping_name = $_POST['shipping_name'];
    $shipping_email = $_POST['shipping_email'];
    $shipping_phone = $_POST['shipping_phone'];
    $shipping_address = $_POST['shipping_address'];
    $shipping_country = $_POST['shipping_country'];
    $shipping_state = $_POST['shipping_state'];
    $total_price = $_POST['total_price'];

    // Create an array to hold product details
    $products = [];

    // Loop through each product in the cart
    foreach ($_POST['products'] as $product) {
        // Append product details to the array
        $products[] = [
            'description' => $product['description'],
            'size' => $product['size'],
            'color' => $product['color'],
            'quantity' => $product['quantity'],
            'image' => $product['image'],
            'price' => $product['price']
        ];
    }

    // Convert the products array to JSON
    $products_json = json_encode($products);

    // Insert the order details into the database with products as JSON
    $sql = "INSERT INTO orders (user_id, total_price, shipping_name, shipping_email, shipping_phone, shipping_address, shipping_country, shipping_state, products)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    // Prepare and bind
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("idsssssss", $user_id, $total_price, $shipping_name, $shipping_email, $shipping_phone, $shipping_address, $shipping_country, $shipping_state, $products_json);
    
    // Execute the statement
    if (!$stmt->execute()) {
        echo "Error: " . $stmt->error;
        exit;
    }

    // Close the statement and connection
    $stmt->close();
    $conn->close();

    header("Location: phonepe_initiate_payment.php");
exit; // Make sure to call exit after header redirection
}
?>