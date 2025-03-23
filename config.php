<?php
$servername = "localhost";
$username = "root";  // XAMPP default username
$password = "";      // XAMPP default password (empty)
$dbname = "hotspot_system_db"; // The name of your database

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
