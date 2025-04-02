<?php
$host = "localhost"; // Database host
$user = "root"; // Default MySQL user in XAMPP
$pass = ""; // No password in XAMPP by default
$dbname = "chirpify"; // Database name

// Create connection
$conn = new mysqli($host, $user, $pass, $dbname); // Establish a connection to the database

// Check connection
if ($conn->connect_error) { // If there is a connection error
    die("Connection failed: " . $conn->connect_error); // Terminate the script and display the error
}

echo "Connected successfully!"; // Display a success message
?>

