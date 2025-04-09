<?php
session_start(); // Start the session to manage user authentication
include 'config.php'; // Include the database configuration file

// Redirect to login page if the user is not logged in
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php"); // Redirect to login page
    exit; // Stop further execution
}

// Fetch all posts along with their authors and like counts
$query = "SELECT posts.id, posts.content, posts.created_at, users.username, 
                 (SELECT COUNT(*) FROM likes WHERE likes.post_id = posts.id) AS like_count 
          FROM posts 
          JOIN users ON posts.user_id = users.id 
          ORDER BY posts.created_at DESC"; // Query to fetch posts and like counts
$result = $conn->query($query); // Execute the query
?>

<head>
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
    <a href="logout.php" class="logout-button">Logout</a> <!-- Logout button -->
    <button onclick="toggleDarkMode()">Toggle Dark Mode</button> <!-- Dark mode toggle button -->

    <h1>Welcome, <?php echo $_SESSION["username"]; ?>!</h1> <!-- Display the logged-in user's username -->
    <div class="container"> <!-- Container for the posts -->

    <!-- Button to navigate to and from the dashboard -->
    <a href="dashboard.php">
        <button>Go to Dashboard</button>
    </a>

    <?php while ($post = $result->fetch_assoc()) { ?> <!-- Loop through all posts -->
        <div>
            <strong><?php echo htmlspecialchars($post["username"]); ?></strong> <!-- Display the author's username -->
            <p><?php echo htmlspecialchars($post["content"]); ?></p> <!-- Display the post content -->
            <p><?php echo $post["like_count"]; ?> Likes</p> <!-- Display the number of likes -->

            <!-- Fetch and display users who liked the post -->
            <p>Liked by:
                <?php
                $post_id = $post['id'];
                $likes_query = "SELECT users.username FROM likes 
                                JOIN users ON likes.user_id = users.id 
                                WHERE likes.post_id = ?";
                $likes_stmt = $conn->prepare($likes_query);
                $likes_stmt->bind_param("i", $post_id);
                $likes_stmt->execute();
                $likes_result = $likes_stmt->get_result();
                $liked_users = [];
                while ($like = $likes_result->fetch_assoc()) {
                    $liked_users[] = htmlspecialchars($like['username']);
                }
                $likes_stmt->close();
                echo implode(', ', $liked_users); // Display the usernames of users who liked the post
                ?>
            </p>

            <button onclick="likePost(<?php echo $post['id']; ?>)">Like</button> <!-- Like button -->
        </div>
    <?php } ?>
    </div> <!-- End of container -->

    <script>
    function likePost(postId) {
        const formData = new FormData();
        formData.append('post_id', postId);

        fetch('like_post.php', {
            method: 'POST',
            body: formData
        }).then(response => {
            if (response.ok) {
                location.reload(); // Reload the page to update the like count
            } else {
                alert('Error liking post');
            }
        });
    }
    </script>
</body>
