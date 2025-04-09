<?php
include 'config.php'; // Include database configuration file
session_start(); // Start the session

// Check if the request method is POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"]); // Get and trim user input email
    $password = $_POST["password"]; // Get user input password

    // Prepare SQL statement to prevent SQL injection
    $stmt = $conn->prepare("SELECT id, username, password, is_admin FROM users WHERE email = ?");
    $stmt->bind_param("s", $email); // Bind the email parameter
    $stmt->execute(); // Execute the query
    $stmt->store_result(); // Store the result

    // Check if any user is found
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $username, $hashed_password, $is_admin); // Bind the retrieved columns
        $stmt->fetch(); // Fetch the result

        // Verify the password
        if (password_verify($password, $hashed_password)) { // Check if password matches hashed password
            $_SESSION["user_id"] = $id; // Store user ID in session
            $_SESSION["username"] = $username; // Store username in session
            $_SESSION["is_admin"] = $is_admin; // Store admin status in session
            header("Location: dashboard.php"); // Redirect to dashboard
            exit; // Exit script
        } else {
            echo "Invalid password!"; // Display error for incorrect password
        }
    } else {
        echo "User not found!"; // Display error if user does not exist
    }

    $stmt->close(); // Close the prepared statement
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login - Chirpify</title>
    <link rel="stylesheet" href="main.css">  <!-- Link to the CSS file -->
</head>
<body>
    <h2>Login</h2>
    <div class="container"> 
    <form method="post" id="login-form"> <!-- Added id -->
        <input type="email" name="email" placeholder="Email" required><br> <!-- Email input field -->
        <input type="password" name="password" placeholder="Password" required><br> <!-- Password input field -->
        <button type="submit">Login</button> <!-- Submit button -->
    </form>
    </div>
</body>
</html>
