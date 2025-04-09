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
<link rel="stylesheet" href="main.css"> <!-- Link to the main CSS file -->
<body>
    <a href="logout.php" id="logout-button" class="logout-button">Logout</a> <!-- Logout button -->
    <div id="container-post" class="container_post"> 
        <h1 id="welcome-message">Welcome, <?php echo $_SESSION["username"]; ?>!</h1> <!-- Display the logged-in user's username -->

        <form id="post-form" method="post" action="upload_post.php" enctype="multipart/form-data"> <!-- Added id -->
            <textarea id="post-content" name="content" placeholder="What's on your mind?"></textarea><br> <!-- Optional content field -->
            <label for="post-image">Upload an image:</label>
            <input id="post-image" type="file" name="post_image" accept="image/*"><br><br> <!-- File input for image upload -->
            <button id="post-submit" type="submit">Post</button> <!-- Submit button -->
        </form>

        <?php while ($post = $result->fetch_assoc()) { ?> <!-- Loop through all posts -->
            <div id="post-<?php echo $post['id']; ?>">
                <strong id="post-author-<?php echo $post['id']; ?>"><?php echo htmlspecialchars($post["username"]); ?></strong> <!-- Display the author's username -->
                <p id="post-content-<?php echo $post['id']; ?>"><?php echo htmlspecialchars($post["content"] ?: "No content provided."); ?></p> <!-- Display the post content -->
                <?php if (!empty($post["image_path"])): ?> <!-- Check if the post has an image -->
                    <img id="post-image-<?php echo $post['id']; ?>" src="<?php echo htmlspecialchars($post["image_path"]); ?>" width="300"> <!-- Display post image -->
                <?php endif; ?>
                <a id="like-link-<?php echo $post['id']; ?>" href="like.php?post_id=<?php echo $post['id']; ?>">Like</a> <!-- Link to like the post -->
            </div>
        <?php } ?>
    </div> <!-- Close the container for posts -->
</body>
</html>