<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: index.html');
    exit();
}

$user_id = $_SESSION['user_id'];
$query = "SELECT * FROM subscriptions WHERE user_id = $user_id";
$result = mysqli_query($conn, $query);
$subscriptions = mysqli_fetch_all($result, MYSQLI_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subscription Plans</title>
</head>
<body>
    <h2>Choose Your Subscription Plan</h2>
    <form action="payment.php" method="POST">
        <select name="package_id">
            <option value="1">1 Day @ Ksh 22</option>
            <option value="2">5 Days @ Ksh 78</option>
            <option value="3">1 Week @ Ksh 109</option>
            <option value="4">1 Month @ Ksh 450</option>
        </select>
        <button type="submit">Pay Now</button>
    </form>
</body>
</html>
