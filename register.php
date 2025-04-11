<?php
include 'config.php'; // Include the database configuration file

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $email = trim($_POST["email"]);
    $password_raw = $_POST["password"];
    $profile_picture = 'defult_user_image.jpg'; // Corrected filename
    $profile_path = 'uploads/profiles/' . $profile_picture;

    // Validate inputs
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("❌ Invalid email format.");
    }

    if (strlen($username) < 3 || strlen($username) > 20) {
        die("❌ Username must be between 3 and 20 characters.");
    }

    if (strlen($password_raw) < 6) {
        die("❌ Password must be at least 6 characters long.");
    }

    // Check if email already exists
    $check_stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $check_stmt->bind_param("s", $email);
    $check_stmt->execute();
    $check_stmt->store_result();

    if ($check_stmt->num_rows > 0) {
        $check_stmt->close();
        die("❌ This email is already registered. <a href='login.php'>Login here</a>");
    }
    $check_stmt->close();

    // Hash password
    $hashed_password = password_hash($password_raw, PASSWORD_DEFAULT);

    // Insert new user
    $stmt = $conn->prepare("INSERT INTO users (username, email, password, profile_picture) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $username, $email, $hashed_password, $profile_path);

    if ($stmt->execute()) {
        echo "✅ Registration successful! <a href='login.php'>Login here</a>";
    } else {
        echo "❌ Oops! Something went wrong. Please try again later.";
        // You can log error details to a file if needed: error_log($stmt->error);
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register - Chirpify</title>
    <link rel="stylesheet" href="main.css">
</head>
<body id="register-page">
    <h2>Register</h2>
    <div class="container">
        <form method="post" id="register-form">
            <input type="text" name="username" placeholder="Username" required><br>
            <input type="email" name="email" placeholder="Email" required><br>
            <input type="password" name="password" placeholder="Password" required><br>
            <button type="submit">Register</button>
        </form>
    </div>
</body>
</html>
