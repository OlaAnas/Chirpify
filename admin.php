<?php
session_start(); 
include 'config.php'; 

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

// Check if the logged-in user is an admin
$stmt = $conn->prepare("SELECT is_admin FROM users WHERE id = ?");
$stmt->bind_param("i", $_SESSION["user_id"]);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

if (!$user || !$user["is_admin"]) {
    echo "Access denied. You are not an admin.";
    exit;
}

// CSRF token
$csrf_token = bin2hex(random_bytes(32));
$_SESSION['csrf_token'] = $csrf_token;

// Handle user deletion
if (isset($_GET["delete"])) {
    $user_id = $_GET["delete"];
    if ($user_id == $_SESSION["user_id"]) {
        echo "You cannot delete yourself!";
    } else {
        $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        if ($stmt->execute()) {
            echo "User deleted successfully!";
        }
        $stmt->close();
    }
}

// Fetch all users
$stmt = $conn->prepare("SELECT id, username, is_admin FROM users");
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Panel - Chirpify</title>
    <link rel="stylesheet" href="main.css">
</head>
<body>
    <div id="admin-body">
    <h2 id="admin-heading">Admin Panel</h2>

    <div id="admin-panel" class="container"> 
        <table id="user-table" border="1">
            <thead id="user-table-header">
                <tr>
                    <th id="column-user-id">User ID</th>
                    <th id="column-username">Username</th>
                    <th id="column-role">Role</th>
                    <th id="column-action">Action</th>
                </tr>
            </thead>
            <tbody id="user-table-body">
                <?php while ($user = $result->fetch_assoc()): ?> 
                    <tr id="user-row-<?php echo $user['id']; ?>">
                        <td id="user-id-<?php echo $user['id']; ?>"><?php echo $user["id"]; ?></td>
                        <td id="username-<?php echo $user['id']; ?>"><?php echo htmlspecialchars($user["username"]); ?></td>
                        <td id="role-<?php echo $user['id']; ?>"><?php echo $user["is_admin"] ? "Admin" : "User"; ?></td>
                        <td id="action-<?php echo $user['id']; ?>">
                            <?php if (!$user["is_admin"]): ?>
                                <a id="delete-user-<?php echo $user['id']; ?>" 
                                   href="admin.php?delete=<?php echo $user["id"]; ?>&csrf_token=<?php echo $csrf_token; ?>" 
                                   onclick="return confirm('Are you sure?')">Delete</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <br>
        <a id="back-to-dashboard" href="dashboard.php">Back to Dashboard</a>
    </div>
    </div>
</body>
</html>
