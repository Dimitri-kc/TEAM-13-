<?php
session_start();
include '../backend/config/db_connect.php';  // defines $conn

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $user_id = $_POST['user_ID'] ?? 0; // or use session user ID if logged in
    $rating = (int)$_POST['rating'];
    $comment = trim($_POST['comment']);

    // Validate input
    if ($rating < 1 || $rating > 5 || empty($comment)) {
        die("Invalid input.");
    }

    // Insert review into database
    $stmt = $conn->prepare("INSERT INTO reviews (user_ID, product_ID, rating, comment, review_date) VALUES (?, NULL, ?, ?, NOW())");
    $stmt->bind_param("iis", $user_id, $rating, $comment);

    if ($stmt->execute()) {
        // Redirect back to homepage so the new review appears
        header("Location: ../homepage.php?success=1");
        exit;
    } else {
        die("Error submitting review: " . $conn->error);
    }
}
?>
