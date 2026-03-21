<?php
session_start();
include "../backend/config/db_connect.php"; // adjust path if needed

if (!isset($_SESSION['user_ID'])) {
    exit("Not logged in");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'])) {

    $user_id = $_SESSION['user_ID'];
    $product_id = (int)$_POST['product_id'];

    // Delete favourite from DB
    $sql = "DELETE FROM favourites WHERE user_ID = ? AND product_ID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $user_id, $product_id);
    $stmt->execute();
}

$redirect = $_POST['redirect'] ?? 'favourites.php';
header("Location: $redirect");
exit;
?>

