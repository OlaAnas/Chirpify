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
$stmt = $conn->prepare("SELECT username FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

// Handle profile update
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_username = trim($_POST["username"]);
    $new_password = trim($_POST["password"]);
    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

    // Update username
    if (!empty($new_username)) {
        $stmt = $conn->prepare("UPDATE users SET username = ? WHERE id = ?");
        $stmt->bind_param("si", $new_username, $user_id);
        $stmt->execute();
        $stmt->close();
        $_SESSION["username"] = $new_username; // Update session
    }

    // Update password
    if (!empty($new_password)) {
        $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
        $stmt->bind_param("si", $hashed_password, $user_id);
        $stmt->execute();
        $stmt->close();
    }

    echo "Profile updated successfully!";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile - Chirpify</title>
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
    <button id="darkModeToggle" onclick="toggleDarkMode()">Toggle Dark Mode</button> <!-- Keep this dark mode toggle button -->

<h2>Edit Profile</h2>
<div class="container" id="edit-profile-container"> 

<form method="post" id="edit-profile-form">
    <label for="username">New Username:</label>
    <input type="text" name="username" id="username" value="<?php echo htmlspecialchars($user["username"]); ?>" required><br><br>

    <label for="password">New Password (leave empty if no change):</label>
    <input type="password" name="password" id="password"><br><br>

    <button type="submit" id="save-changes-button">Save Changes</button>
</form>

<form action="upload_profile_picture.php" method="post" enctype="multipart/form-data" id="upload-profile-picture-form">
    <label for="profile_picture">Upload a Profile Picture:</label>
    <input type="file" name="profile_picture" id="profile_picture" accept="image/*">
    <button type="submit" id="upload-button">Upload</button>
</form>

<br>
<a href="dashboard.php" id="dashboard-link">Back to Dashboard</a>

<br>
<!-- Button to navigate back to profile -->
<a href="profile.php" id="back-to-profile-link">
    <button id="back-to-profile-button">Back to Profile</button>
</a>
</div>

</body>
</html>
