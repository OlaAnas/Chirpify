<?php
session_start();
include 'config.php';

if (!isset($_SESSION["user_id"])) {
    die("You must be logged in to upload a profile picture.");
}

$user_id = $_SESSION["user_id"];

// Check if a file has been uploaded
if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == 0) {
    $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
    $file_type = $_FILES['profile_picture']['type'];

    // Validate file type
    if (in_array($file_type, $allowed_types)) {
        $file_name = $_FILES['profile_picture']['name'];
        $file_tmp = $_FILES['profile_picture']['tmp_name'];

        // Generate a unique file name and set the upload directory
        $upload_dir = 'uploads/profiles/';
        if (!is_dir($upload_dir)) {
            if (!mkdir($upload_dir, 0755, true)) {
                die("Failed to create upload directory.");
            }
        }

        $new_file_name = uniqid() . '-' . basename($file_name);
        $file_path = $upload_dir . $new_file_name;

        // Move the uploaded file to the uploads directory
        if (move_uploaded_file($file_tmp, $file_path)) {
            // Update the profile picture in the database
            $stmt = $conn->prepare("UPDATE users SET profile_picture = ? WHERE id = ?");
            $stmt->bind_param("si", $new_file_name, $user_id);
            $stmt->execute();
            $stmt->close();

            echo "Profile picture uploaded successfully!";
        } else {
            die("Error moving the uploaded file. Please check directory permissions.");
        }
    } else {
        die("Invalid file type. Only JPEG, JPG, PNG, and GIF are allowed.");
    }
} else {
    // Check if the user already has a profile picture
    $stmt = $conn->prepare("SELECT profile_picture FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($current_picture);
    $stmt->fetch();
    $stmt->close();

    if (empty($current_picture)) {
        // Set default profile picture if no file is uploaded and no profile picture exists
        $default_picture = 'defult_user_image.jpg'; // Corrected filename
        $stmt = $conn->prepare("UPDATE users SET profile_picture = ? WHERE id = ?");
        $stmt->bind_param("si", $default_picture, $user_id);
        $stmt->execute();
        $stmt->close();
        echo "Default profile picture set.";
    }
}

header("Location: profile.php");
exit;
?>
