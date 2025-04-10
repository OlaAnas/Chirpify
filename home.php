<?php
session_start();
include 'config.php';

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

$query = "SELECT posts.id, posts.content, posts.created_at, users.username, 
                 (SELECT COUNT(*) FROM likes WHERE likes.post_id = posts.id) AS like_count 
          FROM posts 
          JOIN users ON posts.user_id = users.id 
          ORDER BY posts.created_at DESC";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Chirpify Home</title>
    <link rel="stylesheet" href="main.css">
    <script>
        function toggleDarkMode() {
            document.body.classList.toggle('dark-mode');

            const links = document.querySelectorAll('a:not(.navbar a)');
            const buttons = document.querySelectorAll('button:not(.navbar button)');
            links.forEach(link => link.classList.toggle('dark-mode'));
            buttons.forEach(button => button.classList.toggle('dark-mode'));
        }

        function toggleMenu() {
            const menu = document.querySelector('.hamburger-menu .menu');
            menu.style.display = menu.style.display === 'block' ? 'none' : 'block';
        }

        function likePost(postId) {
            const formData = new FormData();
            formData.append('post_id', postId);

            fetch('like_post.php', {
                method: 'POST',
                body: formData
            }).then(response => {
                if (response.ok) {
                    location.reload();
                } else {
                    alert('Error liking post');
                }
            });
        }
    </script>
</head>
<body id="home-page">

    <!-- Navigation -->
    <div class="hamburger-menu">
        <button onclick="toggleMenu()">☰ Menu</button>
        <div class="menu">
            <a href="dashboard.php">Dashboard</a>
            <a href="home.php">Home</a>
            <a href="profile.php">Profile</a>
            <a href="logout.php" class="logout-button">Logout</a>
        </div>
    </div>

    <!-- Dark Mode Toggle -->
    <button id="darkModeToggle" onclick="toggleDarkMode()">Toggle Dark Mode</button>

    <!-- Greeting -->
    <h1 id="welcome-heading">Welcome, <?php echo htmlspecialchars($_SESSION["username"]); ?>!</h1>

    <!-- Post Creation -->
    <div class="container" id="post-container">
        <form method="post" action="home.php" enctype="multipart/form-data" id="create-post-form">
            <textarea name="content" placeholder="What's on your mind?" id="post-textarea"></textarea><br>
            <label for="post_image">Upload an image:</label>
            <input type="file" name="post_image" id="post-image" accept="image/*"><br><br>
            <button type="submit" id="submit-post-button">Post</button>
        </form>

        <!-- Dashboard Navigation -->
        <a href="dashboard.php">
            <button id="dashboard-button">Go to Dashboard</button>
        </a>

        <!-- Display Posts -->
        <?php while ($post = $result->fetch_assoc()) { ?>
            <div class="post" id="post-<?php echo $post['id']; ?>">
                <strong><?php echo htmlspecialchars($post["username"]); ?></strong>
                <p><?php echo htmlspecialchars($post["content"]); ?></p>
                <p><?php echo $post["like_count"]; ?> Likes</p>

                <!-- Liked Users -->
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
                    echo implode(', ', $liked_users);
                    ?>
                </p>

                <!-- Like Button -->
                <button onclick="likePost(<?php echo $post['id']; ?>)" class="like-button">Like</button>
            </div>
        <?php } ?>
    </div>

</body>
</html>
