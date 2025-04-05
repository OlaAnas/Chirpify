<?php
session_start();
include 'config.php';

// Check if the user is logged in and is an admin
if (!isset($_SESSION["user_id"]) || !$_SESSION["is_admin"]) {
    die("Access denied. You must be an admin to perform this action.");
}

if (isset($_POST['user_id'])) {
    $user_id = $_POST['user_id'];

    // Delete the user from the database
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->close();

    header("Location: admin_dashboard.php");
    exit;
} else {
    die("No user ID provided.");
}
?>
