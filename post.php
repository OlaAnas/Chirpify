<?php
session_start(); // Start the session to manage user authentication
include 'config.php'; // Include the database configuration file

// Redirect to login page if the user is not logged in
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php"); // Redirect to login page
    exit(); // Stop further execution
}

// Fetch all posts along with their authors
$query = "SELECT posts.id, posts.content, posts.created_at, users.username 
          FROM posts JOIN users ON posts.user_id = users.id 
          ORDER BY posts.created_at DESC"; // Query to fetch posts
$result = $conn->query($query); // Execute the query
?>

<h1>Welcome, <?php echo $_SESSION["username"]; ?>!</h1> <!-- Display the logged-in user's username -->
<a href="logout.php">Logout</a> <!-- Logout link -->

<form method="post" action="post.php"> <!-- Form to create a new post -->
    <textarea name="content" required></textarea> <!-- Textarea for post content -->
    <button type="submit">Post</button> <!-- Submit button -->
</form>

<?php while ($post = $result->fetch_assoc()) { ?> <!-- Loop through all posts -->
    <div>
        <strong><?php echo htmlspecialchars($post["username"]); ?></strong> <!-- Display the author's username -->
        <p><?php echo htmlspecialchars($post["content"]); ?></p> <!-- Display the post content -->
        <a href="like.php?post_id=<?php echo $post['id']; ?>">Like</a> <!-- Link to like the post -->
    </div>
<?php } ?>
