<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $product = [
    'product_id' => $_POST['product_id'],
    'product_name' => $_POST['product_name'],
    'product_price' => $_POST['product_price'],
    'product_image' => $_POST['product_image'],
  ];

  $_SESSION['wishlist'][$product['product_id']] = $product;
  echo json_encode(['status' => 'success']);
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
  $wishlistItems = isset($_SESSION['wishlist']) ? array_values($_SESSION['wishlist']) : [];
  echo json_encode($wishlistItems);
}

if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
  parse_str(file_get_contents('php://input'), $_DELETE);
  $product_id = $_DELETE['product_id'];

  if (isset($_SESSION['wishlist'][$product_id])) {
    unset($_SESSION['wishlist'][$product_id]);
    echo json_encode(['status' => 'success']);
  } else {
    echo json_encode(['status' => 'error', 'message' => 'Product not found']);
  }
}
?>
