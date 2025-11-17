<?php
include_once __DIR__ . '/db_connect.php';

$action = $_GET['action'] ?? '';

if ($action === 'insert') {
    $product_ID = $_GET['product_ID'];
    $user_id = $_GET['user_id'];
    $rating = $_GET['rating'];
    $comment = $_GET['comment'];

    $sql = "INSERT INTO reviews (product_ID, user_id, rating, comment)
            VALUES ('$product_ID', '$user_id', '$rating', '$comment')";

    if ($conn->query($sql)) {
        echo json_encode(["status" => "success", "message" => "Review added successfully"]);
    } else {
        echo json_encode(["status" => "error", "message" => $conn->error]);
    }
}

if ($action === 'fetch') {
    $product_ID = $_GET['product_ID'];

    $sql = "SELECT r.review_ID, r.rating, r.comment, r.review_date, u.name AS user_name
            FROM reviews r
            JOIN users u ON r.user_id = u.id
            WHERE r.product_ID = '$product_ID'
            ORDER BY r.review_date DESC";

    $result = $conn->query($sql);

    $reviews = [];
    while ($row = $result->fetch_assoc()) {
        $reviews[] = $row;
    }

    echo json_encode($reviews);
}
?>
