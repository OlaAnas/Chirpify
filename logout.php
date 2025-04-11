<?php
session_start(); // Start the session to manage user authentication
session_destroy(); // Destroy all session data to log the user out
header("Location: index.php"); // Redirect to the index page
exit; // Stop further execution
?>
<a href="logout.php" id="logout-link" class="logout-button">Logout</a> <!-- Added id -->
