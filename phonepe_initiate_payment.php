<?php
// phonepe_initiate_payment.php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

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
// const $SHOULDPUBLISHEVENTS=true;

// Initialize PhonePe client
$phonePeClient = new PhonePePaymentClient(MERCHANTID, SALTKEY, SALTINDEX, $env, true);

// Simulate fetching order details
session_start();
$shippingDetails = $_SESSION['shipping_details'];
$totalPrice = 100;  // Example total price (in paise, so ₹100)

// Generate unique transaction ID
$merchantTransactionId = 'PHPSDK' . date("ymdHis") . "payPageTest";

// Add a sleep delay before initiating the request
sleep(5);  // Pauses execution for 5 seconds before making the request

// Build the PhonePe payment request
$request = PgPayRequestBuilder::builder()
    ->mobileNumber($shippingDetails['phone'])
    ->merchantId(MERCHANTID)
    ->merchantUserId('user-id')  // Unique user ID, ensure it meets length/character requirements
    ->amount($totalPrice)
    ->merchantTransactionId($merchantTransactionId)
    ->redirectUrl('https://yourdomain.com/redirect.php')
    ->redirectMode('REDIRECT')  // Set the mode as "REDIRECT"
    ->callbackUrl('https://yourdomain.com/callback.php')  // PhonePe will send a callback notification here
    ->paymentInstrument(InstrumentBuilder::buildPayPageInstrument())  // No need to call build() again here
    ->build();

// Initiate the payment

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
    echo json_encode([
        'status' => 'error',
        'message' => 'Payment initiation failed'
    ]);
}
