<?php
// save_shipping_details.php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Capture the shipping details
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $country = $_POST['country'];
    $state = $_POST['state'];

    // Save these details to the database or session
    // Example: saving to session for simplicity
    session_start();
    $_SESSION['shipping_details'] = [
        'name' => $name,
        'email' => $email,
        'phone' => $phone,
        'address' => $address,
        'country' => $country,
        'state' => $state
    ];

    // Assuming save is successful
    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error']);
}
