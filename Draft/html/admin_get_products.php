<?php
include '../backend/config/db_connect.php';

header('Content-Type: application/json');

// Modify the query to include category_id
$query = "SELECT product_ID, name, price, stock, image, category_id 
          FROM products 
          ORDER BY product_ID ASC";

$result = mysqli_query($conn, $query);

$products = [];

while ($row = mysqli_fetch_assoc($result)) {
    $products[] = $row;
}

echo json_encode($products);
?>