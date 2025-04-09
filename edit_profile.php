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
<div class="container"> 

<form action="upload_profile_picture.php" method="post" enctype="multipart/form-data">
    <label for="profile_picture">Upload a Profile Picture:</label>
    <input type="file" name="profile_picture" accept="image/*">
    <button type="submit">Upload</button>
</form>

<br>
<a href="dashboard.php">Back to Dashboard</a>

<br>
<!-- Button to navigate back to profile -->
<a href="profile.php">
    <button>Back to Profile</button>
</a>
</div>

</body>
</html>
