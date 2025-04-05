<?php
session_start();
include 'config.php';

if (!isset($_SESSION["user_id"])) {
    die("You must be logged in to like or unlike posts.");
}

if (isset($_POST['post_id'])) {
    $user_id = $_SESSION["user_id"];
    $post_id = $_POST['post_id'];

    // Check if the user has already liked the post
    $check_query = "SELECT id FROM likes WHERE post_id = ? AND user_id = ?";
    $stmt = $conn->prepare($check_query);
    $stmt->bind_param("ii", $post_id, $user_id);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // If the user has liked the post, remove the like (unlike)
        $delete_query = "DELETE FROM likes WHERE post_id = ? AND user_id = ?";
        $delete_stmt = $conn->prepare($delete_query);
        $delete_stmt->bind_param("ii", $post_id, $user_id);
        $delete_stmt->execute();
        $delete_stmt->close();
    } else {
        // If the user hasn't liked the post, add the like
        $insert_query = "INSERT INTO likes (user_id, post_id) VALUES (?, ?)";
        $insert_stmt = $conn->prepare($insert_query);
        $insert_stmt->bind_param("ii", $user_id, $post_id);
        $insert_stmt->execute();
        $insert_stmt->close();
    }
}

header("Location: home.php"); // Redirect back to the home page after the action
exit;
?>
