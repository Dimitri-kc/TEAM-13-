<?php
include '../backend/config/db_connect.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $stars = intval($_POST['stars']);
    $title = trim($_POST['title']);
    $text  = trim($_POST['text']);
    $name  = trim($_POST['name']);

    $stmt = $conn->prepare("INSERT INTO general_reviews (stars, title, review_text, name) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isss", $stars, $title, $text, $name);

    if ($stmt->execute()) {
        echo "success";
    } else {
        echo "error: " . $stmt->error; // <-- show MySQL error
    }

    $stmt->close();
    $conn->close();
}
?>