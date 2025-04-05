<?php
// Database connection details
$servername = "localhost";
$username = "root";   // Default username in XAMPP
$password = "";       // Default password in XAMPP (empty)
$dbname = "hotspot_system_db";  // The name of your database

// Create connection to the database
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error); // Show the error
} else {
    echo "Connected successfully!";
}
?>
