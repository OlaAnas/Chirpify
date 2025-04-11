<?php
session_start();
include 'config.php';

// Check if user is logged in
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

// Fetch current user info
$user_id = $_SESSION["user_id"];
$stmt = $conn->prepare("SELECT username, profile_picture FROM users WHERE id = ?");
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
    <title>Edit Profile - Chirpify</title>
    <link rel="stylesheet" href="main.css">
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

<body id="edit-profile-page">
    <a href="logout.php" class="logout-button" id="logout-button">Logout</a>
    <button id="dark-mode-toggle" onclick="toggleDarkMode()">Toggle Dark Mode</button>

    <h2 id="edit-profile-heading">Edit Profile</h2>

    <div class="container" id="edit-profile-container"> 

        <!-- Display Profile Picture -->
        <?php if ($user_details && $user_details['profile_picture'] && file_exists('uploads/profiles/' . $user_details['profile_picture'])): ?>
            <img src="uploads/profiles/<?php echo htmlspecialchars($user_details['profile_picture']); ?>" alt="Profile Picture" width="150">
        <?php else: ?>
            <img src="uploads/profiles/default_user_image.jpg" alt="Default Profile Picture" width="150">
        <?php endif; ?>

        <!-- Edit Username and Password Form -->
        <form method="post" id="edit-profile-form">
            <label id="label" for="username">New Username:</label>
            <input type="text" name="username" id="username" value="<?php echo htmlspecialchars($user_details["username"]); ?>" required><br><br>

            <label id="label" for="password">New Password (leave empty if no change):</label>
            <input type="password" name="password" id="password"><br><br>

            <button type="submit" id="save-changes-button">Save Changes</button>
        </form>

        <br>

        <!-- Upload Profile Picture Form -->
        <form action="upload_profile_picture.php" method="post" enctype="multipart/form-data" id="upload-profile-picture-form">
            <label id="label" for="profile_picture">Upload a Profile Picture:</label>
            <input type="file" name="profile_picture" id="profile_picture" accept="image/*">
            <button type="submit" id="upload-button">Upload</button>
        </form>

        <br>

        <!-- Navigation Links -->
        <a href="dashboard.php" id="dashboard-link">Back to Dashboard</a>
        <br><br>

        <a href="profile.php" id="back-to-profile-link">
            <button type="button" id="back-to-profile-button">Back to Profile</button>
        </a>

    </div>
</body>
</html>
