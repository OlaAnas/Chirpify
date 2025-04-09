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

// Fetch profile picture and username
$query = "SELECT username, profile_picture FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id); // Bind the user ID
$stmt->execute(); // Execute the query
$result = $stmt->get_result(); // Get the result
$user_details = $result->fetch_assoc(); // Fetch the user details
$stmt->close(); // Close the statement
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($user['username']); ?>'s Profile - Chirpify</title>
    <link rel="stylesheet" href="main.css"> <!-- Link to the main CSS file -->
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
    <div class="hamburger-menu">
        <button onclick="toggleMenu()">☰ Menu</button>
        <div class="menu">
            <a href="dashboard.php">Dashboard</a>
            <a href="home.php">Home</a>
            <a href="profile.php">Profile</a>
            <button id="darkModeToggle" onclick="toggleDarkMode()">Toggle Dark Mode</button>
            <a href="logout.php" class="logout-button">Logout</a>
        </div>
    </div>

    <a href="logout.php" class="logout-button">Logout</a> <!-- Logout button -->
    <button id="darkModeToggle" onclick="toggleDarkMode()">Toggle Dark Mode</button> <!-- Keep this dark mode toggle button -->
    <div class="container"> 

<h2><?php echo htmlspecialchars($user['username'] ?? 'Unknown User'); ?>'s Profile</h2> <!-- Display the user's username -->

<!-- Display profile picture -->
<?php if ($user_details && $user_details['profile_picture'] && file_exists('uploads/profiles/' . $user_details['profile_picture'])): ?>
    <img src="uploads/profiles/<?php echo htmlspecialchars($user_details['profile_picture']); ?>" alt="Profile Picture" width="150">
<?php else: ?>
    <img src="uploads/profiles/defult_user_image.jpg" alt="Default Profile Picture" width="150">
<?php endif; ?>

<!-- Form to upload a profile picture -->
<form action="upload_profile_picture.php" method="post" enctype="multipart/form-data">
    <label for="profile_picture">Upload a Profile Picture:</label>
    <input type="file" name="profile_picture" accept="image/*" required>
    <button type="submit">Upload</button>
</form>

<h3>Recent Posts</h3>
<ul id="profile-posts-list"> <!-- Already has id -->
    <?php while ($post = $posts->fetch_assoc()): ?> <!-- Loop through all posts -->
        <li>
            <p><?php echo htmlspecialchars($post['content']); ?></p> <!-- Display the post content -->
            <small>Posted on <?php echo $post['created_at']; ?></small> <!-- Display the post creation date -->
        </li>
    <?php endwhile; ?>
</ul>

<br>
<a href="dashboard.php">Back to Dashboard</a> <!-- Link to go back to the dashboard -->

<br>
<!-- Button to navigate to edit profile -->
<a href="edit_profile.php">
    <button>Edit Profile</button>
</a>
</div> <!-- End of container -->
</body>
</html>
