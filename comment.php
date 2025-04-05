<?php
session_start();
include 'config.php';

if (!isset($_SESSION["user_id"])) {
    die("You must be logged in to comment.");
}

$user_id = $_SESSION["user_id"];
$post_id = $_POST["post_id"];
$comment = $_POST["comment"];

// Correct the column name in the INSERT query to match the database schema
$stmt = $conn->prepare("INSERT INTO comments (post_id, user_id, comment_text) VALUES (?, ?, ?)"); // Use 'comment_text' instead of 'comment'
$stmt->bind_param("iis", $post_id, $user_id, $comment); // Bind parameters
$stmt->execute(); // Execute the query
$stmt->close(); // Close the statement

header("Location: dashboard.php");
exit;
?>
