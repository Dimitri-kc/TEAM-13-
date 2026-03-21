<?php
include '../backend/config/db_connect.php';

$product_ID = $_GET['product_ID'];

$query = "SELECT * FROM product_reviews
          WHERE product_ID = '$product_ID'
          ORDER BY created_at DESC";

$result = mysqli_query($conn, $query);

$reviews = [];

while ($row = mysqli_fetch_assoc($result)) {
    $reviews[] = $row;
}

echo json_encode($reviews);
?>