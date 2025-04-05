<?php
$host = "localhost";
$user = "root"; // Default MySQL user in XAMPP
$pass = ""; // No password in XAMPP by default
$dbname = "chirpify";

// Create connection
$conn = new mysqli($host, $user, $pass, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

echo "Connected successfully!";
?>
