<?php
include '../backend/config/db_connect.php';

header('Content-Type: application/json');

$query = "SELECT product_ID, name, price, stock, image 
          FROM products 
          ORDER BY product_ID ASC 
          LIMIT 25";

$result = mysqli_query($conn, $query);

$products = [];

while ($row = mysqli_fetch_assoc($result)) {
    $products[] = $row;
}

echo json_encode($products);
?>