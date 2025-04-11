<?php
session_start();
include 'config.php';

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$user_id = isset($_GET['id']) ? intval($_GET['id']) : $_SESSION['user_id'];

// Fetch user info
$stmt = $conn->prepare("SELECT id, username FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

if (!$user) {
    echo "<p>User not found.</p>";
    exit;
}

// Fetch user's posts
$stmt = $conn->prepare("SELECT id, content, created_at FROM posts WHERE user_id = ? ORDER BY created_at DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$posts = $stmt->get_result();
$stmt->close();

// Fetch profile picture
$query = "SELECT username, profile_picture FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user_details = $result->fetch_assoc();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($user['username']); ?>'s Profile - Chirpify</title>
    <link rel="stylesheet" href="main.css">
    <script>
        function toggleDarkMode() {
            document.body.classList.toggle('dark-mode');
            const links = document.querySelectorAll('a');
            links.forEach(link => link.classList.toggle('dark-mode'));
            const buttons = document.querySelectorAll('button');
            buttons.forEach(button => button.classList.toggle('dark-mode'));
            const containers = document.querySelectorAll('.container');
            containers.forEach(container => container.classList.toggle('dark-mode'));
            document.body.style.backgroundColor = document.body.classList.contains('dark-mode') ? '#333' : '#f4f7f6';
        }

        function toggleMenu() {
            const menu = document.querySelector('.hamburger-menu .menu');
            menu.style.display = menu.style.display === 'block' ? 'none' : 'block';
        }
    </script>
</head>
<body id="profile-page">
    <div class="hamburger-menu">
        <button onclick="toggleMenu()">☰ Menu</button>
        <div class="menu">
            <a href="dashboard.php">Dashboard</a>
            <a href="home.php">Home</a>
            <a href="profile.php">Profile</a>
            <a href="logout.php" class="logout-button">Logout</a>
        </div>
    </div>
    <button id="darkModeToggle" onclick="toggleDarkMode()">Toggle Dark Mode</button>

    <div class="container">
        <h2><?php echo htmlspecialchars($user['username']); ?>'s Profile</h2>

        <!-- Display profile picture -->
        <?php if ($user_details && $user_details['profile_picture'] && file_exists('uploads/profiles/' . $user_details['profile_picture'])): ?>
            <img class="profile-photo" src="uploads/profiles/<?php echo htmlspecialchars($user_details['profile_picture']); ?>" alt="Profile Picture">
        <?php else: ?>
            <img class="profile-photo" src="uploads/profiles/defult_user_image.jpg" alt="Default Profile Picture"> <!-- Corrected filename -->
        <?php endif; ?>

        <!-- Show upload form only if it's your own profile -->
        <?php if ($_SESSION['user_id'] === $user_id): ?>
            <form action="upload_profile_picture.php" method="post" enctype="multipart/form-data" id="upload-profile-picture-form">
                <label for="profile_picture">Upload a Profile Picture:</label>
                <input type="file" name="profile_picture" accept="image/*" required>
                <button type="submit">Upload</button>
            </form>
        <?php endif; ?>

        <h3>Recent Posts</h3>
        <ul id="profile-posts-list">
            <?php while ($post = $posts->fetch_assoc()): ?>
                <li>
                    <p><?php echo htmlspecialchars($post['content']); ?></p>
                    <small>Posted on <?php echo $post['created_at']; ?></small>
                </li>
            <?php endwhile; ?>
        </ul>

        <br>
        <a href="dashboard.php">Back to Dashboard</a>

        <?php if ($_SESSION['user_id'] === $user_id): ?>
            <br><a href="edit_profile.php"><button>Edit Profile</button></a>
        <?php endif; ?>
    </div>
</body>
</html>
