<?php
session_start();

$product_id = (int)$_POST['product_id'];
$product_name = $_POST['product_name'] ?? '';
$product_price = $_POST['product_price'] ?? '';
$product_image = $_POST['product_image'] ?? '';
$redirect = $_POST['redirect'] ?? 'favourites.php';

// Make sure the session array exists
if (!isset($_SESSION['favourites'])) {
    $_SESSION['favourites'] = [];
}

// Only add if product_id is valid and not already saved
if ($product_id > 0) {

    $exists = false;
    foreach ($_SESSION['favourites'] as $item) {
        if ($item['id'] == $product_id) {
            $exists = true;
            break;
        }
    }

    if (!$exists) {
        $_SESSION['favourites'][] = [
            'id' => $product_id,
            'name' => $product_name,
            'price' => $product_price,
            'image' => $product_image
        ];
    }
}

header("Location: $redirect");
exit;
?>
