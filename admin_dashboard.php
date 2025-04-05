<?php
session_start();
include 'config.php';

// Check if the user is logged in and is an admin
if (!isset($_SESSION["user_id"]) || !$_SESSION["is_admin"]) {
    die("Access denied. You must be an admin to view this page.");
}

$query = "SELECT id, username, email, is_admin FROM users";
$result = $conn->query($query);
?>

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
