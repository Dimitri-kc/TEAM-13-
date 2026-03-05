<?php
session_start();
include "../backend/config/db_connect.php"; // adjust path if needed

if (!isset($_SESSION['user_ID'])) {
    exit("Not logged in");
}

$redirect = $_POST['redirect'] ?? 'favourites.php';
if (!is_string($redirect) || preg_match('/^https?:\/\//i', $redirect)) {
    $redirect = 'favourites.php';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'])) {

    $user_id = $_SESSION['user_ID'];
    $product_id = (int)$_POST['product_id'];

    try {
        $sql = "DELETE FROM favourites WHERE user_ID = ? AND product_ID = ?";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new Exception('Failed to prepare favourites delete: ' . $conn->error);
        }
        $stmt->bind_param("ii", $user_id, $product_id);
        $stmt->execute();
        $stmt->close();
    } catch (Throwable $e) {
        error_log('favourite_remove.php: ' . $e->getMessage());
    }
}

header("Location: $redirect");
exit;
?>

