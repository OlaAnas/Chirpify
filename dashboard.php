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
    if (isset($_POST["content"]) || isset($_FILES["post_image"])) { 
        // Handle new post submission
        $content = trim($_POST["content"] ?? ""); // Get and sanitize the post content
        $user_id = $_SESSION["user_id"]; // Get the logged-in user's ID
        $image_path = null;

        // Check if a file has been uploaded
        if (isset($_FILES['post_image']) && $_FILES['post_image']['error'] == 0) {
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
            $file_type = $_FILES['post_image']['type'];

            // Validate file type
            if (in_array($file_type, $allowed_types)) {
                $file_name = $_FILES['post_image']['name'];
                $file_tmp = $_FILES['post_image']['tmp_name'];

                // Generate a unique file name and set the upload directory
                $upload_dir = 'uploads/posts/';
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0755, true); // Create the directory if it doesn't exist
                }
                $new_file_name = uniqid() . '-' . basename($file_name);
                $image_path = $upload_dir . $new_file_name;

                // Move the uploaded file to the uploads directory
                if (!move_uploaded_file($file_tmp, $image_path)) {
                    die("Error uploading the image.");
                }
            } else {
                die("Invalid file type. Only JPEG, PNG, and GIF are allowed.");
            }
        }

        // Insert the new post into the database
        if (!empty($content) || $image_path) { // Ensure either content or image is provided
            $stmt = $conn->prepare("INSERT INTO posts (user_id, content, image_path) VALUES (?, ?, ?)");
            $stmt->bind_param("iss", $user_id, $content, $image_path);
            $stmt->execute();
            $stmt->close();
            header("Location: dashboard.php"); // Redirect to refresh the page
            exit; // Stop further execution
        } else {
            echo "Error: You must provide either text or an image to post.";
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
$stmt = $conn->prepare("SELECT posts.id, posts.content, posts.image_path, posts.created_at, users.username, posts.user_id,
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
    <link rel="stylesheet"  href="main.css"> <!-- Link to the main CSS file -->
    <script>
        function toggleDarkMode() {
            document.body.classList.toggle('dark-mode');
            const links = document.querySelectorAll('a');
            links.forEach(link => link.classList.toggle('dark-mode'));
            const buttons = document.querySelectorAll('button');
            buttons.forEach(button => button.classList.toggle('dark-mode'));
        }

        function toggleMenu() {
            const menu = document.querySelector('.hamburger-menu .menu');
            menu.style.display = menu.style.display === 'block' ? 'none' : 'block';
        }
    </script>
</head>

<body>
    <div id="dashboard-container" class="container"> 
    <h2 id="welcome-message">Welcome, <?php echo htmlspecialchars($_SESSION["username"]); ?>!</h2> <!-- Display the logged-in user's username -->
    <div class="hamburger-menu">
        <button onclick="toggleMenu()">☰ Menu</button>
        <div class="menu">
            <a href="dashboard.php">Dashboard</a>
            <a href="home.php">Home</a>
            <a href="profile.php">Profile</a>
            <a  href="logout.php" >Logout</a> <!-- Logout button -->
        </div>
    </div>

    <button id="darkModeToggle" onclick="toggleDarkMode()">Toggle Dark Mode</button> <!-- Keep this dark mode toggle button outside the navbar -->
    
    <div class="container"> 
   

    <!-- Admin Dashboard link -->
    <?php if (isset($_SESSION["is_admin"]) && $_SESSION["is_admin"]): ?>
        <p id="admin-status">You are logged in as an Admin.</p> <!-- Display admin status -->
        <a id="admin-dashboard-link" href="admin_dashboard.php">Admin Dashboard</a> <!-- Link to admin dashboard -->
    <?php endif; ?>

    <!-- Unified form for posting content and images -->
    <form id="post-form" method="post" action="dashboard.php" enctype="multipart/form-data">
        <textarea id="post-content" name="content" placeholder="What's on your mind?"></textarea><br> <!-- Optional content field -->
        <label for="post-image">Upload an image:</label>
        <input id="post-image" type="file" name="post_image" accept="image/*"><br><br> <!-- File input for image upload -->
        <button id="post-submit" type="submit">Post</button> <!-- Submit button -->
    </form>

    <h2 id="all-posts-title">All Posts</h2>
    <ul id="posts-list"> <!-- Added id="posts-list" -->
        <?php while ($post = $result->fetch_assoc()): ?> <!-- Loop through all posts -->
            <li id="post-<?php echo $post['id']; ?>">
                <p id="post-content-<?php echo $post['id']; ?>"><strong id="post-author-<?php echo $post['id']; ?>"><?php echo htmlspecialchars($post['username']); ?></strong>: <?php echo htmlspecialchars($post['content']); ?></p> <!-- Display post content -->
                <?php if ($post['image_path']): ?> <!-- Check if the post has an image -->
                    <img id="post-image-<?php echo $post['id']; ?>" 
                         src="<?php echo htmlspecialchars($post['image_path']); ?>" 
                         style="width: 300px; height: 300px; object-fit: cover; border-radius: 10px;" 
                         alt="Post image"> <!-- Display post image -->
                <?php endif; ?>
                <small id="post-date-<?php echo $post['id']; ?>">Posted on <?php echo $post['created_at']; ?></small><br> <!-- Display post creation date -->

                <!-- Like button -->
                <form id="like-form-<?php echo $post['id']; ?>" method="post" action="like.php"> <!-- Form to handle likes -->
                    <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>"> <!-- Hidden input for post ID -->
                    <button id="like-button-<?php echo $post['id']; ?>" type="submit">❤️ Like (<?php echo $post['like_count']; ?>)</button> <!-- Like button with like count -->
                </form>

                <!-- Delete button (only for post owner) -->
                <?php if ($_SESSION["user_id"] == $post["user_id"]): ?> <!-- Check if the logged-in user owns the post -->
                    <form id="delete-form-<?php echo $post['id']; ?>" method="post"> <!-- Form to delete a post -->
                        <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>"> <!-- Hidden input for post ID -->
                        <button id="delete-button-<?php echo $post['id']; ?>" type="submit" name="delete">🗑️ Delete</button> <!-- Delete button -->
                    </form>
                <?php endif; ?>

                <!-- Comment form -->
                <form id="comment-form-<?php echo $post['id']; ?>" method="post" action="comment.php"> <!-- Form to add a comment -->
                    <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>"> <!-- Hidden input for post ID -->
                    <textarea id="comment-content-<?php echo $post['id']; ?>" name="comment" required placeholder="Write a comment..."></textarea><br> <!-- Textarea for comment -->
                    <button id="comment-submit-<?php echo $post['id']; ?>" type="submit">Post Comment</button> <!-- Submit button for comment -->
                </form>

                <!-- Display comments for each post -->
                <div id="comments-container-<?php echo $post['id']; ?>" class="comments">
                <h4 id="comments-title-<?php echo $post['id']; ?>">Comments:</h4>
                <ul id="comments-list-<?php echo $post['id']; ?>"> <!-- Added id="comments-list-{post_id}" -->
                    <?php
                    $post_id = $post['id'];
                    $comment_query = "SELECT comments.id, comments.comment_text, users.username, comments.created_at
                                      FROM comments 
                                      JOIN users ON comments.user_id = users.id
                                      WHERE comments.post_id = ?
                                      ORDER BY comments.created_at DESC"; // Query to fetch comments for the post
                    $comment_stmt = $conn->prepare($comment_query);
                    $comment_stmt->bind_param("i", $post_id);
                    $comment_stmt->execute();
                    $comment_result = $comment_stmt->get_result();
                    while ($comment = $comment_result->fetch_assoc()): ?> <!-- Loop through all comments -->
                        <li id="comment-<?php echo $comment['id']; ?>"> <!-- Ensure unique ID for each comment -->
                            <p id="comment-content-<?php echo $comment['id']; ?>"><strong id="comment-author-<?php echo $comment['id']; ?>"><?php echo htmlspecialchars($comment['username']); ?></strong>: <?php echo htmlspecialchars($comment['comment_text']); ?></p>
                            <small id="comment-date-<?php echo $comment['id']; ?>">Commented on <?php echo $comment['created_at']; ?></small><br>
                        </li>
                    <?php endwhile; ?>
                    <?php $comment_stmt->close(); ?>
                </ul>
                </div>
            </li>
        <?php endwhile; ?>
    </ul>
    </div> <!-- End of dashboard container -->
</body>
</html>
<?php $stmt->close(); // Close the statement ?>
