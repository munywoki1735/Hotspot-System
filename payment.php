<?php
session_start();  // Ensure session is started
include 'config.php';  // Include your database connection file

// Ensure that package_id is passed from the form and is valid
if (!isset($_POST['package_id']) || empty($_POST['package_id'])) {
    die("Package ID is missing or invalid.");
}
$package_id = $_POST['package_id']; // Get package ID from POST

// Fetch package details from the database using a prepared statement
$query = "SELECT * FROM subscriptions WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $package_id);  // Bind package_id as integer
$stmt->execute();
$result = $stmt->get_result();

if (!$result) {
    error_log("Error fetching package details: " . mysqli_error($conn)); // Log error if query fails
    echo "Error fetching package details: " . mysqli_error($conn);
    exit();
}

$package = $result->fetch_assoc();

if (!$package) {
    echo "Invalid package selection.";
    exit();
}

// Get the phone number from the form (you can modify this as per your requirement)
$phone_number = isset($_POST['phone_number']) ? $_POST['phone_number'] : '';  // Ensure phone number is passed in the form
if (empty($phone_number)) {
    echo "Phone number is required.";
    exit();
}

$amount = $package['package_price'];  // Amount to pay based on the selected package

// Insert subscription data into subscriptions table
$insert_query = "INSERT INTO subscriptions (package_name, package_price, phone_number, account_reference, transaction_desc, status, payment_status) 
                 VALUES (?, ?, ?, ?, ?, 'inactive', 'pending')";  // Insert initial data
$stmt_insert = $conn->prepare($insert_query);
$stmt_insert->bind_param("sdssss", $package['package_name'], $amount, $phone_number, "Payment for Subscription", "Subscription payment for plan: " . $package['package_name']);
$insert_result = $stmt_insert->execute();

if (!$insert_result) {
    error_log("Error inserting subscription data: " . mysqli_error($conn)); // Log error if query fails
    echo "Error inserting subscription data: " . mysqli_error($conn);
    exit();
}

// Get access token from the M-Pesa API
$access_token = getAccessToken();
if (!$access_token) {
    echo "Error: Could not retrieve access token.";
    exit();
}

// M-Pesa Shortcode (replace with actual shortcode)
$shortcode = "YOUR_SHORTCODE";  // Replace with your actual shortcode

// M-Pesa API Request to initiate payment (STK Push)
$api_url = "https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest";

// Prepare data to send for M-Pesa request
$data = [
    'Shortcode' => $shortcode,
    'PhoneNumber' => $phone_number,
    'Amount' => $amount,
    'AccountReference' => "Payment for Subscription", // You can customize this
    'TransactionDesc' => "Subscription payment for plan: " . $package['package_name'],
    'CallbackURL' => 'https://a192-102-210-222-2.ngrok-free.app/mpesa/payment_callback.php'  // ngrok public URL for the callback
];

// Send cURL request to M-Pesa API for payment processing
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $api_url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . $access_token,
    'Content-Type: application/json'
]);

$response = curl_exec($ch);
curl_close($ch);

// Check if response was successful
if ($response) {
    // Process response and handle success/failure
    $response_data = json_decode($response, true);

    // Log the response data for debugging
    error_log("M-Pesa Response: " . print_r($response_data, true));

    if (isset($response_data['ResponseCode']) && $response_data['ResponseCode'] == 0) {
        // Payment successful, update the subscription status in the database
        $update_query = "UPDATE subscriptions SET status = 'active', payment_status = 'completed' WHERE phone_number = ?";
        $stmt_update = $conn->prepare($update_query);
        $stmt_update->bind_param("s", $phone_number);  // Bind phone_number for update
        $update_result = $stmt_update->execute();

        if ($update_result) {
            echo "Payment successful! Your subscription is now active.";
        } else {
            error_log("Error executing query: " . mysqli_error($conn)); // Log the error
            echo "Error executing query: " . mysqli_error($conn);
        }
    } else {
        echo "Payment failed! Error: " . $response_data['ResponseDescription'];
    }
} else {
    echo "Error: No response from M-Pesa API.";
    error_log("M-Pesa API Response: No response received"); // Log the no response error
}

// Function to get Access Token from M-Pesa API
function getAccessToken() {
    global $mpesa_api_key, $mpesa_api_secret;
    
    // Set up the credentials for M-Pesa API
    $credentials = base64_encode($mpesa_api_key . ':' . $mpesa_api_secret);
    $url = "https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials";
    
    // cURL request to get the access token
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Basic ' . $credentials
    ]);
    $response = curl_exec($ch);
    curl_close($ch);

    // Log the response for debugging
    error_log("Access Token Response: " . print_r($response, true));

    if ($response) {
        $response_data = json_decode($response, true);
        return isset($response_data['access_token']) ? $response_data['access_token'] : null;
    } else {
        return null;
    }
}
?>
