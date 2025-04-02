<?php
session_start(); // Start the session to manage user authentication
include 'config.php'; // Include the database configuration file

// Redirect to login page if the user is not logged in
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php"); // Redirect to login page
    exit; // Stop further execution
}

// Handle form submissions
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["content"])) { 
        // Handle new post submission
        $content = trim($_POST["content"]); // Get and sanitize the post content
        $user_id = $_SESSION["user_id"]; // Get the logged-in user's ID

        if (!empty($content)) { // Ensure the content is not empty
            // Insert the new post into the database
            $stmt = $conn->prepare("INSERT INTO posts (user_id, content) VALUES (?, ?)");
            $stmt->bind_param("is", $user_id, $content); // Bind parameters
            $stmt->execute(); // Execute the query
            $stmt->close(); // Close the statement
            header("Location: dashboard.php"); // Redirect to refresh the page
            exit; // Stop further execution
        }
    } elseif (isset($_POST["like"])) {
        // Handle post like
        $post_id = $_POST["post_id"]; // Get the post ID to like
        $user_id = $_SESSION["user_id"]; // Get the logged-in user's ID

        // Check if the user has already liked the post
        $check_like = $conn->prepare("SELECT * FROM likes WHERE user_id = ? AND post_id = ?");
        $check_like->bind_param("ii", $user_id, $post_id); // Bind parameters
        $check_like->execute(); // Execute the query
        $result = $check_like->get_result(); // Get the result

        if ($result->num_rows == 0) { // If not already liked
            // Insert a new like into the database
            $stmt = $conn->prepare("INSERT INTO likes (user_id, post_id) VALUES (?, ?)");
            $stmt->bind_param("ii", $user_id, $post_id); // Bind parameters
            $stmt->execute(); // Execute the query
            $stmt->close(); // Close the statement
        }
    } elseif (isset($_POST["delete"])) {
        // Handle post deletion
        $post_id = $_POST["post_id"]; // Get the post ID to delete
        $user_id = $_SESSION["user_id"]; // Get the logged-in user's ID

        // Check if the post belongs to the logged-in user
        $check_post = $conn->prepare("SELECT * FROM posts WHERE id = ? AND user_id = ?");
        $check_post->bind_param("ii", $post_id, $user_id); // Bind parameters
        $check_post->execute(); // Execute the query
        $result = $check_post->get_result(); // Get the result

        if ($result->num_rows > 0) { // If the post belongs to the user
            // Delete the post from the database
            $stmt = $conn->prepare("DELETE FROM posts WHERE id = ?");
            $stmt->bind_param("i", $post_id); // Bind parameters
            $stmt->execute(); // Execute the query
            $stmt->close(); // Close the statement
        }

        header("Location: dashboard.php"); // Redirect to refresh the page
        exit; // Stop further execution
    }
}

// Fetch all posts along with their like counts
$stmt = $conn->prepare("SELECT posts.id, posts.content, posts.created_at, users.username, posts.user_id,
                        (SELECT COUNT(*) FROM likes WHERE likes.post_id = posts.id) AS like_count
                        FROM posts 
                        JOIN users ON posts.user_id = users.id 
                        ORDER BY posts.created_at DESC");
$stmt->execute(); // Execute the query
$result = $stmt->get_result(); // Get the result set
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard - Chirpify</title>
</head>
<body>
    <h2>Welcome, <?php echo htmlspecialchars($_SESSION["username"]); ?>!</h2> <!-- Display the logged-in user's username -->
    <form method="post"> <!-- Form to create a new post -->
        <textarea name="content" placeholder="What's on your mind?" required></textarea><br> <!-- Textarea for post content -->
        <button type="submit">Post</button> <!-- Submit button -->
    </form>

    <h2>All Posts</h2>
    <ul>
        <?php while ($post = $result->fetch_assoc()): ?> <!-- Loop through all posts -->
            <li>
                <p><strong><?php echo htmlspecialchars($post['username']); ?></strong>: <?php echo htmlspecialchars($post['content']); ?></p> <!-- Display post content -->
                <small>Posted on <?php echo $post['created_at']; ?></small><br> <!-- Display post creation date -->

                <!-- Like button -->
                <form method="post" action="like.php"> <!-- Form to like a post -->
                    <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>"> <!-- Hidden input for post ID -->
                    <button type="submit" name="like">❤️ Like (<?php echo $post['like_count']; ?>)</button> <!-- Like button with like count -->
                </form>

                <!-- Delete button (only for post owner) -->
                <?php if ($_SESSION["user_id"] == $post["user_id"]): ?> <!-- Check if the logged-in user owns the post -->
                    <form method="post"> <!-- Form to delete a post -->
                        <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>"> <!-- Hidden input for post ID -->
                        <button type="submit" name="delete">🗑️ Delete</button> <!-- Delete button -->
                    </form>
                <?php endif; ?>
            </li>
        <?php endwhile; ?>
    </ul>

    <br>
    <a href="logout.php">Logout</a> <!-- Logout link -->
</body>
</html>
<?php $stmt->close(); // Close the statement ?>
