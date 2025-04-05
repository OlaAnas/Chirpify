<?php
session_start();
include 'config.php';

// Ensure the 'is_admin' session variable is set when the user logs in.
if (!isset($_SESSION["is_admin"])) {
    $stmt = $conn->prepare("SELECT is_admin FROM users WHERE id = ?");
    $stmt->bind_param("i", $_SESSION["user_id"]);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $_SESSION["is_admin"] = $user["is_admin"] ?? 0; // Default to 0 if not set
    $stmt->close();
}

// Check if the user is logged in and is an admin
if (!isset($_SESSION["user_id"]) || !$_SESSION["is_admin"]) {
    die("Access denied. You must be an admin to view this page.");
}

$query = "SELECT id, username, email, is_admin FROM users";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
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
    <button onclick="toggleDarkMode()">Toggle Dark Mode</button> <!-- Dark mode toggle button -->
<h2>Admin Dashboard</h2>

<!-- Admin Dashboard link -->
<?php if (isset($_SESSION["is_admin"]) && $_SESSION["is_admin"]): ?>
    <a href="admin_dashboard.php">Admin Dashboard</a>
<?php endif; ?>

<h3>Manage Users</h3>
<table border="1">
    <thead>
        <tr>
            <th>ID</th>
            <th>Username</th>
            <th>Email</th>
            <th>Admin Status</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($user = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($user['id']); ?></td>
                <td><?php echo htmlspecialchars($user['username']); ?></td>
                <td><?php echo htmlspecialchars($user['email']); ?></td>
                <td><?php echo $user['is_admin'] ? 'Yes' : 'No'; ?></td>
                <td>
                    <!-- Delete user button (except for admin) -->
                    <?php if ($user['is_admin'] == 0): ?>
                        <form method="post" action="delete_user.php" style="display:inline;">
                            <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                            <button type="submit" onclick="return confirm('Are you sure you want to delete this user?');">Delete</button>
                        </form>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endwhile; ?>
    </tbody>
</table>
</body>
</html>
