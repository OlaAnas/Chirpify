<?php
session_start();
include 'config.php';

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

// Updated query to include image_path
$query = "SELECT posts.id, posts.content, posts.created_at, posts.image_path, users.username 
          FROM posts 
          JOIN users ON posts.user_id = users.id 
          ORDER BY posts.created_at DESC";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Home - Chirpify</title>
    <link rel="stylesheet" href="main.css">
</head>
<body id="post-page">

    <a href="logout.php" id="logout-button" class="logout-button">Logout</a>

    <div id="container-post" class="container_post"> 
        <h1 id="welcome-message">Welcome, <?php echo htmlspecialchars($_SESSION["username"]); ?>!</h1>

        <form id="post-form" method="post" action="upload_post.php" enctype="multipart/form-data">
            <textarea id="post-content" name="content" placeholder="What's on your mind?"></textarea><br>
            <label for="post-image">Upload an image:</label>
            <input id="post-image" type="file" name="post_image" accept="image/*"><br><br>
            <button id="post-submit" type="submit">Post</button>
        </form>

        <?php while ($post = $result->fetch_assoc()) { ?> 
            <div id="post-<?php echo $post['id']; ?>" class="post-card">
                <strong id="post-author-<?php echo $post['id']; ?>">
                    <?php echo htmlspecialchars($post["username"]); ?>
                </strong>
                <p id="post-content-<?php echo $post['id']; ?>">
                    <?php echo htmlspecialchars($post["content"] ?: "No content provided."); ?>
                </p>

                <?php if (!empty($post["image_path"])): ?>
                    <img id="post-image-<?php echo $post['id']; ?>" 
                         src="<?php echo htmlspecialchars($post["image_path"]); ?>" 
                         width="300" alt="Post image">
                <?php endif; ?>

                <a id="like-link-<?php echo $post['id']; ?>" 
                   href="like.php?post_id=<?php echo $post['id']; ?>">Like</a>
            </div>
        <?php } ?>
    </div>

</body>
</html>
