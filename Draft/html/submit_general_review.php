<?php
include '../backend/config/db_connect.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $conn->set_charset("utf8mb4");

    $stars = intval($_POST['stars']);
    $title = trim($_POST['title']);
    $text  = trim($_POST['text']);
    $name  = trim($_POST['name']);

    // Validate required fields
    if (empty($title) || empty($text) || empty($name) || $stars < 1 || $stars > 5) {
        echo "error";
        exit;
    }

    // Validate max 200 chars
    if (mb_strlen($text, 'UTF-8') > 200) {
        echo "error";
        exit;
    }

    // Sanitize for safe output later
    $title = htmlspecialchars($title, ENT_QUOTES, 'UTF-8');
    $text  = htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
    $name  = htmlspecialchars($name, ENT_QUOTES, 'UTF-8');

    $stmt = $conn->prepare("INSERT INTO general_reviews (stars, title, review_text, name) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isss", $stars, $title, $text, $name);

    if ($stmt->execute()) {
        echo "success";
    } else {
        error_log("Review insert error: " . $stmt->error);
        echo "error";
    }

    $stmt->close();
    $conn->close();
}
?>