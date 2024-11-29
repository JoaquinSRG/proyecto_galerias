<?php
require 'db.php';

// Start the session if it's not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if the user is an administrator
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

// Check if user_id is provided via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_id'])) {
    $user_id = intval($_POST['user_id']); // Get the user ID to delete

    try {
        // Step 1: Get all images associated with the user
        $stmt = $pdo->prepare("SELECT file_path FROM images WHERE user_id = :user_id");
        $stmt->execute(['user_id' => $user_id]);
        $images = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Step 2: Delete the files from the server
        foreach ($images as $image) {
            if (file_exists($image['file_path'])) {
                unlink($image['file_path']); // Delete the file
            }
        }

        // Step 3: Delete all image records from the database
        $stmt = $pdo->prepare("DELETE FROM images WHERE user_id = :user_id");
        $stmt->execute(['user_id' => $user_id]);

        // Step 4: Delete the user from the database
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = :id");
        $stmt->execute(['id' => $user_id]);

        // Redirect back to the admin panel with a success message
        header("Location: admin.php?status=success");
        exit;

    } catch (PDOException $e) {
        echo "Error deleting user: " . $e->getMessage();
    }
} else {
    echo "Invalid request. <a href='admin.php'>Go back</a>";
}
?>

