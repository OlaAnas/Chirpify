<?php
session_start();
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);

    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $token = bin2hex(random_bytes(50));

        // Insert token with expiration
        $stmt = $conn->prepare("INSERT INTO password_resets (user_id, token, expires_at) VALUES (?, ?, DATE_ADD(NOW(), INTERVAL 1 HOUR))");
        $stmt->bind_param("is", $user['id'], $token);
        $stmt->execute();

        $reset_link = "http://localhost/Chirpify/reset_password.php?token=" . $token;
        mail($email, "Password Reset Request", "Click the link to reset your password: $reset_link");

        echo "A password reset link has been sent to your email.";
    } else {
        echo "No account found with that email.";
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <link rel="stylesheet" href="main.css">
</head>
<body id="forgot-password-page">
    <h2 id="forgot-password-heading">Forgot Password</h2>

    <form method="post" id="forgot-password-form">
        <label for="email">Enter your email:</label><br>
        <input type="email" name="email" id="email-input" placeholder="Email address" required><br><br>
        <button type="submit" id="send-reset-button">Send Reset Link</button>
    </form>
</body>
</html>
