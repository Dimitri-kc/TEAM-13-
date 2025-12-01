<?php
include_once __DIR__ . '/db_connect.php';
header('Content-Type: application/json');

$action = $_GET['action'] ?? '';

if ($action === 'insert') {
    $product_ID = $_POST['product_ID'] ?? 0;
    $user_ID = $_POST['user_ID'] ?? 0;
    $rating = $_POST['rating'] ?? 0;
    $comment = $_POST['comment'] ?? '';

    $stmt = $conn->prepare("INSERT INTO reviews (product_ID, user_ID, rating, comment) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiis", $product_ID, $user_ID, $rating, $comment);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Review added successfully"]);
    } else {
        echo json_encode(["status" => "error", "message" => $conn->error]);
    }

    $stmt->close();
}
elseif ($action === 'fetch') {
    $product_ID = $_GET['product_ID'] ?? 0;

    $stmt = $conn->prepare("
        SELECT r.review_ID, r.rating, r.comment, r.review_date, u.name AS user_name
        FROM reviews r
        JOIN users u ON r.user_ID = u.user_ID
        WHERE r.product_ID = ?
        ORDER BY r.review_date DESC
    ");
    $stmt->bind_param("i", $product_ID);
    $stmt->execute();
    $result = $stmt->get_result();

    $reviews = [];
    while ($row = $result->fetch_assoc()) {
        $reviews[] = $row;
    }

    echo json_encode($reviews);
    $stmt->close();
}
else {
    echo json_encode(["status" => "error", "message" => "Invalid or missing action"]);
}

$conn->close();
?>
