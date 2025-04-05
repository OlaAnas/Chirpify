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
            // Delete likes associated with the post
            $delete_likes = $conn->prepare("DELETE FROM likes WHERE post_id = ?");
            $delete_likes->bind_param("i", $post_id); // Bind parameters
            $delete_likes->execute(); // Execute the query
            $delete_likes->close(); // Close the statement

            // Delete comments associated with the post
            $delete_comments = $conn->prepare("DELETE FROM comments WHERE post_id = ?");
            $delete_comments->bind_param("i", $post_id); // Bind parameters
            $delete_comments->execute(); // Execute the query
            $delete_comments->close(); // Close the statement

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
    <link rel="stylesheet" href="main.css"> <!-- Link to the main CSS file -->
    <script>
        function toggleDarkMode() {
            document.body.classList.toggle('dark-mode');
            const links = document.querySelectorAll('a');
            links.forEach(link => link.classList.toggle('dark-mode'));
            const buttons = document.querySelectorAll('button');
            buttons.forEach(button => button.classList.toggle('dark-mode'));
        }
    </script>
</head>

<body>
    <button onclick="toggleDarkMode()">Toggle Dark Mode</button>
    <div class="dashboard-container"> <!-- Dark mode toggle button -->
    <h2>Welcome, <?php echo htmlspecialchars($_SESSION["username"]); ?>!</h2> <!-- Display the logged-in user's username -->

    <!-- Button to navigate to profile -->
    <a href="profile.php">
        <button>Go to Profile</button>
    </a>

    <!-- Admin Dashboard link -->
    <?php if (isset($_SESSION["is_admin"]) && $_SESSION["is_admin"]): ?>
        <a href="admin_dashboard.php">Admin Dashboard</a>
    <?php endif; ?>

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
                <form method="post" action="like.php"> <!-- Form to handle likes -->
                    <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>"> <!-- Hidden input for post ID -->
                    <button type="submit">❤️ Like (<?php echo $post['like_count']; ?>)</button> <!-- Like button with like count -->
                </form>

                <!-- Delete button (only for post owner) -->
                <?php if ($_SESSION["user_id"] == $post["user_id"]): ?> <!-- Check if the logged-in user owns the post -->
                    <form method="post"> <!-- Form to delete a post -->
                        <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>"> <!-- Hidden input for post ID -->
                        <button type="submit" name="delete">🗑️ Delete</button> <!-- Delete button -->
                    </form>
                <?php endif; ?>

                <!-- Comment form -->
                <form method="post" action="comment.php"> <!-- Form to add a comment -->
                    <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>"> <!-- Hidden input for post ID -->
                    <textarea name="comment" required placeholder="Write a comment..."></textarea><br> <!-- Textarea for comment -->
                    <button type="submit">Post Comment</button> <!-- Submit button for comment -->
                </form>

                <!-- Display comments for each post -->
                <div class="comments">
                <h4>Comments:</h4>
                <?php
                $post_id = $post['id'];
                $comment_query = "SELECT comments.comment_text, users.username, comments.created_at
                                  FROM comments 
                                  JOIN users ON comments.user_id = users.id
                                  WHERE comments.post_id = $post_id
                                  ORDER BY comments.created_at DESC"; // Query to fetch comments for the post
                $comment_result = $conn->query($comment_query); // Execute the query
                while ($comment = $comment_result->fetch_assoc()): ?> <!-- Loop through all comments -->
                    <p><strong><?php echo htmlspecialchars($comment['username']); ?></strong>: <?php echo htmlspecialchars($comment['comment_text']); ?></p> <!-- Display comment content -->
                    <small>Commented on <?php echo $comment['created_at']; ?></small><br> <!-- Display comment creation date -->
                <?php endwhile; ?>
                </div>
            </li>
        <?php endwhile; ?>
    </ul>

    <br>
    <a href="logout.php">Logout</a> <!-- Logout link -->
    </div> <!-- End of dashboard container -->
</body>
</html>
<?php $stmt->close(); // Close the statement ?>
