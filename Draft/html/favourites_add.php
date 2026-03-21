<?php
session_start();
include '../backend/config/db_connect.php';

// User must be logged in
if (!isset($_SESSION['user_ID'])) {
    header("Location: signin.php");
    exit;
}

$user_id = $_SESSION['user_ID'];
$product_id = (int)($_POST['product_id'] ?? 0);
$redirect = $_POST['redirect'] ?? 'favourites.php';

if ($product_id > 0) {

    // Check if already in favourites
    $check = $conn->prepare("SELECT * FROM favourites WHERE user_ID = ? AND product_ID = ?");
    $check->bind_param("ii", $user_id, $product_id);
    $check->execute();
    $res = $check->get_result();

    if ($res->num_rows === 0) {
        // Insert into DB
        $insert = $conn->prepare("INSERT INTO favourites (user_ID, product_ID) VALUES (?, ?)");
        $insert->bind_param("ii", $user_id, $product_id);
        $insert->execute();
    }
}

header("Location: $redirect");
exit;
