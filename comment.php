<?php
session_start();
include 'config.php';

if (!isset($_SESSION["user_id"])) {
    die("You must be logged in to comment.");
}

$user_id = $_SESSION["user_id"];
$post_id = $_POST["post_id"];
$comment = $_POST["comment"];

// Insert the comment into the database
$stmt = $conn->prepare("INSERT INTO comments (post_id, user_id, comment) VALUES (?, ?, ?)");
$stmt->bind_param("iis", $post_id, $user_id, $comment);
$stmt->execute();
$stmt->close();

header("Location: dashboard.php");
exit;
?>
