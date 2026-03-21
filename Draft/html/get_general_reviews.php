<?php
include '../backend/config/db_connect.php';

$result = $conn->query("SELECT * FROM general_reviews ORDER BY created_at DESC");

$reviews = [];

while ($row = $result->fetch_assoc()) {
    $reviews[] = $row;
}

echo json_encode($reviews);

$conn->close();