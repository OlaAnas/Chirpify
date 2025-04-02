<?php
session_start(); // Start the session to manage user authentication
include 'config.php'; // Include the database configuration file

// Get user ID from URL or session
$user_id = isset($_GET['id']) ? intval($_GET['id']) : $_SESSION['user_id']; // Use the ID from the URL if provided, otherwise use the logged-in user's ID

// Fetch user info
$stmt = $conn->prepare("SELECT id, username FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id); // Bind the user ID
$stmt->execute(); // Execute the query
$result = $stmt->get_result(); // Get the result
$user = $result->fetch_assoc(); // Fetch the user data
$stmt->close(); // Close the statement

// Fetch user's posts
$stmt = $conn->prepare("SELECT id, content, created_at FROM posts WHERE user_id = ? ORDER BY created_at DESC");
$stmt->bind_param("i", $user_id); // Bind the user ID
$stmt->execute(); // Execute the query
$posts = $stmt->get_result(); // Get the result set
$stmt->close(); // Close the statement
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($user['username']); ?>'s Profile - Chirpify</title>
</head>
<body>

<h2><?php echo htmlspecialchars($user['username']); ?>'s Profile</h2> <!-- Display the user's username -->

<h3>Recent Posts</h3>
<ul>
    <?php while ($post = $posts->fetch_assoc()): ?> <!-- Loop through all posts -->
        <li>
            <p><?php echo htmlspecialchars($post['content']); ?></p> <!-- Display the post content -->
            <small>Posted on <?php echo $post['created_at']; ?></small> <!-- Display the post creation date -->
        </li>
    <?php endwhile; ?>
</ul>

<br>
<a href="dashboard.php">Back to Dashboard</a> <!-- Link to go back to the dashboard -->

</body>
</html>
