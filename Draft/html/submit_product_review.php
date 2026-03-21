<?php
include '../backend/config/db_connect.php';

$product_ID = $_POST['product_ID'];
$stars = $_POST['stars'];
$title = $_POST['title'];
$text = $_POST['text'];
$name = $_POST['name'];

$query = "INSERT INTO product_reviews 
(product_ID, stars, title, review_text, name) 
VALUES 
('$product_ID', '$stars', '$title', '$text', '$name')";

if (mysqli_query($conn, $query)) {
    echo "success";
} else {
    echo "error";
}
?>