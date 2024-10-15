<?php
$server = "localhost";
$username = "root";
$password ="";
$database ="merapyareusers";

$conn = mysqli_connect($server, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$ids = $_GET['ids'];
$sql = "SELECT * FROM product_details WHERE id IN ($ids)";
$result = $conn->query($sql);

$products = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
}

echo json_encode($products);

$conn->close();
?>
