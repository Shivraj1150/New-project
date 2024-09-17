<?php
$server = "localhost";
$username = "root";
$password ="";
$database ="merapyareusers";

$conn = mysqli_connect($server, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$productId = intval($_GET['id']);
$sql = "SELECT * FROM product_details WHERE id = $productId";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $product = $result->fetch_assoc();
    echo json_encode($product);
} else {
    echo json_encode([]);
}

$conn->close();
?>
