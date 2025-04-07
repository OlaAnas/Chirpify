<?php
session_start();
include 'config.php';

if (!isset($_SESSION["user_id"])) {
    die("You must be logged in to comment.");
}

$user_id = $_SESSION["user_id"];
$post_id = $_POST["post_id"];
$comment = $_POST["comment"];

// Check if a file has been uploaded
if (isset($_FILES['comment_image']) && $_FILES['comment_image']['error'] == 0) {
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
    $file_type = $_FILES['comment_image']['type'];

    // Validate file type
    if (in_array($file_type, $allowed_types)) {
        $file_name = $_FILES['comment_image']['name'];
        $file_tmp = $_FILES['comment_image']['tmp_name'];

        // Generate a unique file name and set the upload directory
        $upload_dir = 'uploads/comments/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true); // Create the directory if it doesn't exist
        }
        $new_file_name = uniqid() . '-' . basename($file_name);
        $file_path = $upload_dir . $new_file_name;

        // Move the uploaded file to the uploads directory
        if (move_uploaded_file($file_tmp, $file_path)) {
            // Insert the comment with the image path into the database
            $stmt = $conn->prepare("INSERT INTO comments (post_id, user_id, comment_text, image_path) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("iiss", $post_id, $user_id, $comment, $file_path);
            $stmt->execute();
            $stmt->close();
        } else {
            die("Error uploading the image.");
        }
    } else {
        die("Invalid file type. Only JPEG, PNG, and GIF are allowed.");
    }
} else {
    // Insert the comment without an image
    $stmt = $conn->prepare("INSERT INTO comments (post_id, user_id, comment_text) VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $post_id, $user_id, $comment);
    $stmt->execute();
    $stmt->close();
}

header("Location: dashboard.php");
exit;
?>
