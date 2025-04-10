<?php
include 'config.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    $stmt = $conn->prepare("SELECT id, username, password, is_admin FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $username, $hashed_password, $is_admin);
        $stmt->fetch();

        if (password_verify($password, $hashed_password)) {
            $_SESSION["user_id"] = $id;
            $_SESSION["username"] = $username;
            $_SESSION["is_admin"] = $is_admin;
            header("Location: dashboard.php");
            exit;
        } else {
            echo "Invalid password!";
        }
    } else {
        echo "User not found!";
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - Chirpify</title>
    <link rel="stylesheet" href="main.css">
</head>
<body id="login-page">

    <h2 id="login-heading">Login</h2>

    <div class="container" id="login-container">
        <form method="post" id="login-form">
            <input type="email" name="email" id="email-input" placeholder="Email" required><br>
            <input type="password" name="password" id="password-input" placeholder="Password" required><br>
            <button type="submit" id="login-button">Login</button>
        </form>
    </div>

</body>
</html>
