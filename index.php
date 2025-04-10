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
<body id="index-page">
    <div class="container_index" id="container-index">
        <h1 id="welcome-heading">Welcome to Chirpify 🐦</h1>
        <p id="intro-text">Chirpify is the best place to share your thoughts, like posts, and connect with others.</p>
        
        <div class="buttons" id="buttons">
            <a href="register.php" class="signup" id="signup-button">Sign Up</a>
            <a href="login.php" class="login" id="login-button">Log In</a>
        </div>

        <div class="forgot-password" id="forgot-password">
            <p>Forgot your password? <a href="forgot_password.php" id="reset-password-link">Reset it here</a>.</p>
        </div>

        <div class="features" id="features-section">
            <h2 id="features-heading">Why Choose Chirpify?</h2>
            <ul id="features-list">
                <li id="feature-posting">🌟 Create and share your posts with the world.</li>
                <li id="feature-liking">❤️ Like and appreciate posts from others.</li>
                <li id="feature-security">🔒 Secure platform with encrypted passwords.</li>
                <li id="feature-editing">🛠️ Edit your profile and customize your experience.</li>
                <li id="feature-community">👥 Connect with a vibrant community of users.</li>
            </ul>
        </div>
    </div>

</body>
</html>
