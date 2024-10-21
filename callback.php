<?php
// callback.php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Start session if needed
session_start();

// Database connection
include 'partials/_dbconnect.php'; // Ensure the database connection is set

// Log the fact that the callback is being executed
file_put_contents('callback_execution.log', "Callback executed at " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);

// Check if the request method is GET
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Log the request method for debugging
    file_put_contents('callback_execution.log', "Request method: GET\n", FILE_APPEND);

    // Log the query parameters
    file_put_contents('callback_raw_data.log', json_encode($_GET));

    // Get the transactionId and status from the query parameters
    $transactionId = $_GET['transactionId'] ?? null;
    $paymentStatus = $_GET['status'] ?? null;

    // Ensure the required parameters are present
    if (is_null($transactionId) || is_null($paymentStatus)) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid callback data']);
        exit();
    }

    // Fetch order details if payment was successful
    if ($paymentStatus === 'SUCCESS') {
        // Fetch order details from `orders` table using the transaction ID
        $sql = "SELECT * FROM orders WHERE transaction_id = ? LIMIT 1";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $transactionId);
        $stmt->execute();
        $orderResult = $stmt->get_result();
        
        if ($orderResult->num_rows > 0) {
            $order = $orderResult->fetch_assoc();

            // Insert the payment and order details into the `payments` table
            $paymentSql = "
                INSERT INTO payments (
                    user_id, total_price, transaction_id, payment_status, 
                    payment_method, shipping_name, shipping_email, 
                    shipping_phone, shipping_address, shipping_country, 
                    shipping_state, products
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ";

            $paymentMethod = 'PhonePe';  // Can be dynamic if needed
            $productsJson = json_encode([
                'description' => $order['product_description'],
                'size' => $order['product_size'],
                'color' => $order['product_color'],
                'quantity' => $order['product_quantity'],
                'image' => $order['product_image']
            ]);

            $paymentStmt = $conn->prepare($paymentSql);
            $paymentStmt->bind_param(
                "idsssssssss", 
                $order['user_id'], $order['total_price'], $transactionId, $paymentStatus, $paymentMethod, 
                $order['shipping_name'], $order['shipping_email'], 
                $order['shipping_phone'], $order['shipping_address'], 
                $order['shipping_country'], $order['shipping_state'], 
                $productsJson
            );

            if ($paymentStmt->execute()) {
                echo json_encode(['status' => 'success', 'message' => 'Payment details saved successfully']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Failed to save payment details']);
            }

            $paymentStmt->close();
        } else {
            echo json_encode(['status' => 'error', 'message' => 'No order found for this transaction']);
        }

        $stmt->close();
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Payment failed or pending']);
    }

    // Close the database connection
    $conn->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
?>
