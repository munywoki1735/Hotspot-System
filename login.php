<?php
session_start();
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Clean the input to avoid SQL injection
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    // Query to check if the username exists in the database
    $query = "SELECT * FROM users WHERE username = '$username'";
    $result = mysqli_query($conn, $query);

    // Check if the query was successful and the username exists
    if (mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);

        // Verify the password using the hashed password in the database
        if (password_verify($password, $user['password'])) {
            // Password is correct, set the session variable
            $_SESSION['user_id'] = $user['id'];
            header('Location: subscription.php'); // Redirect to the subscription page
        } else {
            // If the password is incorrect
            echo "Invalid credentials";
        }
    } else {
        // If the username doesn't exist
        echo "Invalid credentials";
    }
}
?>
