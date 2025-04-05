<?php
session_start(); // Start the session to manage user authentication
include 'config.php'; // Include the database configuration file

// Check if the user is logged in
if (!isset($_SESSION["user_id"])) {
    die("You must be logged in to like posts.");
}

$user_id = $_SESSION["user_id"]; // Get the logged-in user's ID
$post_id = $_POST["post_id"] ?? null; // Get the post ID from the POST request

if (!$post_id) { // Check if the post ID is valid
    die("Invalid request.");
}

// Check if the user has already liked the post
$stmt = $conn->prepare("SELECT id FROM likes WHERE user_id = ? AND post_id = ?");
$stmt->bind_param("ii", $user_id, $post_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) { // If the user has not already liked the post
    // Insert a new like into the database
    $stmt = $conn->prepare("INSERT INTO likes (user_id, post_id) VALUES (?, ?)");
    $stmt->bind_param("ii", $user_id, $post_id);
    $stmt->execute();
} else { // If the user has already liked the post, remove the like (toggle functionality)
    $stmt = $conn->prepare("DELETE FROM likes WHERE user_id = ? AND post_id = ?");
    $stmt->bind_param("ii", $user_id, $post_id);
    $stmt->execute();
}

$stmt->close(); // Close the statement

// Redirect back to the referring page
header("Location: " . $_SERVER["HTTP_REFERER"]);
exit;
?>
