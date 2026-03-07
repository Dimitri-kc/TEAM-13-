<?php
session_start();
include '../backend/config/db_connect.php';

// User must be logged in
if (!isset($_SESSION['user_ID'])) {
    http_response_code(401);
    exit("Must be signed in to favourite");
}

$user_id = $_SESSION['user_ID'];
$product_id = (int)($_POST['product_id'] ?? 0);
$redirect = $_POST['redirect'] ?? 'favourites.php';

$noRedirect = $redirect === 'false' || $redirect === false;

if (!$noRedirect) {
    if (!is_string($redirect) || preg_match('/^https?:\/\//i', $redirect)) {
        $redirect = 'favourites.php';
    }
}

if ($product_id > 0) {
    try {
        $createTableSql = "CREATE TABLE IF NOT EXISTS favourites (
            favourite_ID INT AUTO_INCREMENT PRIMARY KEY,
            user_ID INT NOT NULL,
            product_ID INT NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            UNIQUE KEY uniq_user_product (user_ID, product_ID),
            INDEX idx_user_id (user_ID),
            INDEX idx_product_id (product_ID)
        )";

        if (!$conn->query($createTableSql)) {
            throw new Exception('Failed to ensure favourites table: ' . $conn->error);
        }

        $insert = $conn->prepare("INSERT IGNORE INTO favourites (user_ID, product_ID) VALUES (?, ?)");
        if (!$insert) {
            throw new Exception('Failed to prepare favourites insert: ' . $conn->error);
        }
        $insert->bind_param("ii", $user_id, $product_id);
        $insert->execute();
        $insert->close();
    } catch (Throwable $e) {
        error_log('favourites_add.php: ' . $e->getMessage());
    }
}

if ($noRedirect) {
    http_response_code(200);
    exit;
} else {
    header("Location: $redirect");
    exit;
}
