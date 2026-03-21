<?php
include '../backend/config/db_connect.php';

$product_ID = intval($_POST['product_ID']);

$query = "DELETE FROM products WHERE product_ID = $product_ID";

if (mysqli_query($conn, $query)) {
    echo "success";
} else {
    echo "error";
}
?>