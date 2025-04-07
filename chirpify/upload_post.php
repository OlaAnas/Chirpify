<?php
session_start();
include '../config.php'; // Include the database configuration file

if (!isset($_SESSION["user_id"])) {
    die("You must be logged in to post.");
}

$user_id = $_SESSION["user_id"];
$content = isset($_POST["content"]) ? trim($_POST["content"]) : null; // Get the post content if provided

// Check if a file has been uploaded
if (isset($_FILES['post_image']) && $_FILES['post_image']['error'] == 0) {
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
    $file_type = $_FILES['post_image']['type'];

    // Validate file type
    if (in_array($file_type, $allowed_types)) {
        $file_name = $_FILES['post_image']['name'];
        $file_tmp = $_FILES['post_image']['tmp_name'];

        // Generate a unique file name and set the upload directory
        $upload_dir = '../uploads/posts/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true); // Create the directory if it doesn't exist
        }
        $new_file_name = uniqid() . '-' . basename($file_name);
        $file_path = $upload_dir . $new_file_name;

        // Move the uploaded file to the uploads directory
        if (move_uploaded_file($file_tmp, $file_path)) {
            // Insert the post with the image path into the database
            $stmt = $conn->prepare("INSERT INTO posts (user_id, content, image_path) VALUES (?, ?, ?)");
            $stmt->bind_param("iss", $user_id, $content, $file_path);
            $stmt->execute();
            $stmt->close();
        } else {
            die("Error uploading the image.");
        }
    } else {
        die("Invalid file type. Only JPEG, PNG, and GIF are allowed.");
    }
} elseif (!empty($content)) {
    // Insert the post without an image if only content is provided
    $stmt = $conn->prepare("INSERT INTO posts (user_id, content) VALUES (?, ?)");
    $stmt->bind_param("is", $user_id, $content);
    $stmt->execute();
    $stmt->close();
} else {
    die("You must provide either content or an image to post.");
}

header("Location: ../dashboard.php");
exit;
?>