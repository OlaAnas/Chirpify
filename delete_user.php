<?php
session_start();
include 'config.php';

// Check if the user is logged in and is an admin
if (!isset($_SESSION["user_id"]) || !$_SESSION["is_admin"]) {
    die("Access denied. You must be an admin to perform this action.");
}

// Delete posts and their associated comments and likes before deleting the user
if (isset($_POST['user_id'])) {
    $user_id = $_POST['user_id'];

    // Delete comments associated with the user's posts
    $delete_post_comments = $conn->prepare("DELETE comments FROM comments JOIN posts ON comments.post_id = posts.id WHERE posts.user_id = ?");
    $delete_post_comments->bind_param("i", $user_id);
    $delete_post_comments->execute();
    $delete_post_comments->close();

    // Delete likes associated with the user's posts
    $delete_post_likes = $conn->prepare("DELETE likes FROM likes JOIN posts ON likes.post_id = posts.id WHERE posts.user_id = ?");
    $delete_post_likes->bind_param("i", $user_id);
    $delete_post_likes->execute();
    $delete_post_likes->close();

    // Delete the user's posts
    $delete_posts = $conn->prepare("DELETE FROM posts WHERE user_id = ?");
    $delete_posts->bind_param("i", $user_id);
    $delete_posts->execute();
    $delete_posts->close();

    // Delete comments made by the user
    $delete_comments = $conn->prepare("DELETE FROM comments WHERE user_id = ?");
    $delete_comments->bind_param("i", $user_id);
    $delete_comments->execute();
    $delete_comments->close();

    // Delete likes made by the user
    $delete_likes = $conn->prepare("DELETE FROM likes WHERE user_id = ?");
    $delete_likes->bind_param("i", $user_id);
    $delete_likes->execute();
    $delete_likes->close();

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
