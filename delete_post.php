<?php
session_start(); // Start the session to manage user authentication
include 'config.php'; // Include the database configuration file

if (!isset($_SESSION["user_id"])) { // Check if the user is logged in
    die("You must be logged in to delete posts."); // Terminate the script if not logged in
}

$user_id = $_SESSION["user_id"]; // Get the logged-in user's ID
$post_id = $_POST["post_id"]; // Get the post ID from the form submission

// Fetch the post to check if it's owned by the current user
$stmt = $conn->prepare("SELECT user_id FROM posts WHERE id = ?");
$stmt->bind_param("i", $post_id); // Bind the post ID
$stmt->execute(); // Execute the query
$result = $stmt->get_result(); // Get the result
$post = $result->fetch_assoc(); // Fetch the post data

if ($post['user_id'] == $user_id) { // Check if the post belongs to the logged-in user
    // Delete the post if it belongs to the logged-in user
    $stmt = $conn->prepare("DELETE FROM posts WHERE id = ?");
    $stmt->bind_param("i", $post_id); // Bind the post ID
    $stmt->execute(); // Execute the query
    $stmt->close(); // Close the statement
    echo "Post deleted successfully!"; // Display success message
} else {
    echo "You cannot delete someone else's post."; // Display error message
}

header("Location: dashboard.php"); // Redirect to the dashboard
exit; // Stop further execution
?>
