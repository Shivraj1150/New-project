<?php
// phonepe_initiate_payment.php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start(); // Start the session

require_once 'vendor/autoload.php';

use PhonePe\payments\v1\PhonePePaymentClient;
use PhonePe\Env;
use PhonePe\payments\v1\models\request\builders\PgPayRequestBuilder;
use PhonePe\payments\v1\models\request\builders\InstrumentBuilder;

// PhonePe credentials
const MERCHANTID = 'ATMOSTUAT';
const SALTKEY = '58a63b64-574d-417a-9214-066bee1e4caa';
const SALTINDEX = '1';
$env = Env::UAT;  // Use Env::PRODUCTION for live

// Initialize PhonePe client
$phonePeClient = new PhonePePaymentClient(MERCHANTID, SALTKEY, SALTINDEX, $env, true);

// Database connection
include 'partials/_dbconnect.php'; // Ensure this file connects to your database
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Assuming you have the user ID stored in a session
$user_id = $_SESSION['user_id'];

// Debugging: Check if user_id is set
if (empty($user_id)) {
    echo json_encode(['status' => 'error', 'message' => 'User ID is not set in the session.']);
    exit();
}

// Fetch total_price from the database
$sql = "SELECT total_price FROM orders WHERE user_id = ? ORDER BY created_at DESC LIMIT 1"; // Get the latest order
echo "Executing SQL: " . $sql . " with user_id: " . $user_id; // Debugging SQL statement

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
if (!$stmt->execute()) {
    echo json_encode(['status' => 'error', 'message' => 'SQL error: ' . $stmt->error]);
    exit();
}
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $order = $result->fetch_assoc();
    $totalPrice = (int)($order['total_price'] * 100); // Convert to paise

    // Generate unique transaction ID
    $merchantTransactionId = 'PHPSDK' . date("ymdHis") . "payPageTest";

    // Add a sleep delay before initiating the request
    sleep(5);  // Pauses execution for 5 seconds before making the request

    // Build the PhonePe payment request
    $request = PgPayRequestBuilder::builder()
        ->merchantId(MERCHANTID)
        ->merchantUserId($user_id)  // Unique user ID
        ->amount($totalPrice) // Use the fetched amount
        ->merchantTransactionId($merchantTransactionId)
        ->redirectUrl('http://localhost/New-project/callback.php') // Update with your redirect URL
        ->redirectMode('REDIRECT')  // Set the mode as "REDIRECT"
        ->callbackUrl('http://localhost/New-project/callback.php')  // PhonePe will send a callback notification here
        ->paymentInstrument(InstrumentBuilder::buildPayPageInstrument())  // No need to call build() again here
        ->build();
    
    // Log the request payload before sending
    file_put_contents('request.log', json_encode($request)); // Log the request for debugging

    // Initiate the payment
    try {
        $response = $phonePeClient->pay($request);
        
        // Check if the payment initiation was successful
        $instrumentResponse = $response->getInstrumentResponse();
        if ($instrumentResponse) {
            // Get the payment URL
            $paymentUrl = $instrumentResponse->getRedirectInfo()->getUrl();
            
            // Redirect the user to the payment URL
            header("Location: " . $paymentUrl);
            exit();
        } else {
            // Log the response for debugging
            file_put_contents('response.log', json_encode($response));
            echo json_encode([
                'status' => 'error',
                'message' => 'Payment initiation failed'
            ]);
        }
    } catch (PhonePe\common\exceptions\PhonePeException $e) {
        // Log the exception message for debugging
        file_put_contents('error.log', $e->getMessage());
        echo json_encode(['status' => 'error', 'message' => 'Payment initiation error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'No order found for the user.'
    ]);
}

// Close the database connection
$stmt->close();
$conn->close();
?>
