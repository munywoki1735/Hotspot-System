<?php
session_start();
include 'config.php';

// Assuming the user selects a subscription plan
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $package_id = $_POST['package_id'];
    $user_id = $_SESSION['user_id'];

    // Fetch package details
    $query = "SELECT * FROM subscriptions WHERE id = $package_id";
    $result = mysqli_query($conn, $query);
    $package = mysqli_fetch_assoc($result);

    // Initialize M-Pesa API integration here
    $phone_number = 'user_phone_number';  // Get this dynamically
    $amount = $package['price'];  // Amount to pay

    // Example of API call to M-Pesa (this part needs real API credentials)
    $api_url = "https://sandbox.safaricom.co.ke/mpesa";
    $access_token = "YOUR_ACCESS_TOKEN";  // Obtain through M-Pesa API authentication
    $shortcode = "YOUR_SHORTCODE";  // M-Pesa shortcode

    // M-Pesa API Request Example
    $data = array(
        'Shortcode' => $shortcode,
        'PhoneNumber' => $phone_number,
        'Amount' => $amount
    );

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $api_url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Authorization: Bearer ' . $access_token,
        'Content-Type: application/json'
    ));

    $response = curl_exec($ch);
    curl_close($ch);

    if ($response) {
        // Handle successful payment response
        // Mark subscription as active in the database
        echo "Payment successful!";
    } else {
        // Handle failed payment response
        echo "Payment failed!";
    }
}
?>
