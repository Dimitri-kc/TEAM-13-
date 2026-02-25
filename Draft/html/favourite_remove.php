<?php
session_start();

$product_id = (int)$_POST['product_id'];
$redirect = $_POST['redirect'] ?? 'favourites.php';

if (isset($_SESSION['favourites'])) {
    $_SESSION['favourites'] = array_filter($_SESSION['favourites'], function($fav) use ($product_id) {
        return $fav['id'] != $product_id;
    });
}

header("Location: $redirect");
exit;
?>
