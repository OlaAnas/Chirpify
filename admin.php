<?php
session_start(); // Start the session to manage user authentication
include 'config.php'; // Include the database configuration file

// Redirect to login page if the user is not logged in
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php"); // Redirect to login page
    exit; // Stop further execution
}

// Check if the logged-in user is an admin
$stmt = $conn->prepare("SELECT is_admin FROM users WHERE id = ?");
$stmt->bind_param("i", $_SESSION["user_id"]); // Bind the user ID
$stmt->execute(); // Execute the query
$result = $stmt->get_result(); // Get the result
$user = $result->fetch_assoc(); // Fetch the user data
$stmt->close(); // Close the statement

if (!$user || !$user["is_admin"]) { // If the user is not an admin
    echo "Access denied. You are not an admin."; // Display an error message
    exit; // Stop further execution
}

// Handle user deletion
if (isset($_GET["delete"])) {
    $user_id = $_GET["delete"]; // Get the user ID to delete

    // Prevent admin from deleting themselves
    if ($user_id == $_SESSION["user_id"]) {
        echo "You cannot delete yourself!"; // Display an error message
    } else {
        $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
        $stmt->bind_param("i", $user_id); // Bind the user ID
        if ($stmt->execute()) { // Execute the query
            echo "User deleted successfully!"; // Display success message
        }
        $stmt->close(); // Close the statement
    }
}

// Fetch all users
$stmt = $conn->prepare("SELECT id, username, is_admin FROM users");
$stmt->execute(); // Execute the query
$result = $stmt->get_result(); // Get the result set
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Panel - Chirpify</title>
</head>
<body>
    <h2>Admin Panel</h2>
    
    <table border="1">
        <tr>
            <th>User ID</th>
            <th>Username</th>
            <th>Role</th>
            <th>Action</th>
        </tr>
        <?php while ($user = $result->fetch_assoc()): ?> <!-- Loop through all users -->
        <tr>
            <td><?php echo $user["id"]; ?></td> <!-- Display user ID -->
            <td><?php echo htmlspecialchars($user["username"]); ?></td> <!-- Display username -->
            <td><?php echo $user["is_admin"] ? "Admin" : "User"; ?></td> <!-- Display role -->
            <td>
                <?php if (!$user["is_admin"]): ?> <!-- Only allow deletion of non-admin users -->
                    <a href="admin.php?delete=<?php echo $user["id"]; ?>" onclick="return confirm('Are you sure?')">Delete</a> <!-- Delete link -->
                <?php endif; ?>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>

    <br>
    <a href="dashboard.php">Back to Dashboard</a> <!-- Link to go back to the dashboard -->
</body>
</html>
