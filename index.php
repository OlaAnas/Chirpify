<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to Chirpify</title>
    <link rel="stylesheet" href="main.css"> <!-- Link to the main CSS file -->
</head>
<body>
    <div class="container_index">
        <h1>Welcome to Chirpify 🐦</h1>
        <p>Chirpify is the best place to share your thoughts, like posts, and connect with others.</p>
        
        <div class="buttons">
            <a href="register.php" class="signup">Sign Up</a>
            <a href="login.php" class="login">Log In</a>
        </div>

        <div class="forgot-password">
            <p>Forgot your password? <a href="forgot_password.php">Reset it here</a>.</p>
        </div>

        <div class="features">
            <h2>Why Choose Chirpify?</h2>
            <ul id="features-list"> <!-- Re-added id="features-list" -->
                <li>🌟 Create and share your posts with the world.</li>
                <li>❤️ Like and appreciate posts from others.</li>
                <li>🔒 Secure platform with encrypted passwords.</li>
                <li>🛠️ Edit your profile and customize your experience.</li>
                <li>👥 Connect with a vibrant community of users.</li>
            </ul>
        </div>
    </div>

</body>
</html>
