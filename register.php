<?php
include 'config.php'; // Include the database configuration file

if ($_SERVER["REQUEST_METHOD"] == "POST") { // Check if the form is submitted
    $username = trim($_POST["username"]); // Get and sanitize the username
    $email = trim($_POST["email"]); // Get and sanitize the email
    $password = password_hash($_POST["password"], PASSWORD_DEFAULT); // Hash the password for security

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) { // Validate email format
        die("Invalid email format.");
    }
    if (strlen($username) < 3 || strlen($username) > 20) { // Validate username length
        die("Username must be between 3 and 20 characters.");
    }

    // Insert user into database
    $stmt = $conn->prepare("INSERT INTO users (username, email, password, profile_picture) VALUES (?, ?, ?, 'uploads/profiles/default_user_image.jpg')");
    $stmt->bind_param("sss", $username, $email, $password); // Bind parameters

    if ($stmt->execute()) { // Execute the query
        echo "Registration successful! <a href='login.php'>Login here</a>"; // Display success message
    } else {
        echo "Error: " . $stmt->error; // Display error message
    }

    $stmt->close(); // Close the statement
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register - Chirpify</title>
    <link rel="stylesheet" href="main.css">  <!-- Link to the CSS file -->
</head>
<body>
    <h2>Register</h2>
    <div class="container">
    <form method="post" id="register-form"> <!-- Added id -->
        <input type="text" name="username" placeholder="Username" required><br> <!-- Input for username -->
        <input type="email" name="email" placeholder="Email" required><br> <!-- Input for email -->
        <input type="password" name="password" placeholder="Password" required><br> <!-- Input for password -->
        <button type="submit">Register</button> <!-- Submit button -->
    </form>
    </div>
</body>
</html>
