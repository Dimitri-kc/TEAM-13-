<?php
include '../backend/config/db_connect.php';

// Get form data
$name = mysqli_real_escape_string($conn, $_POST['name']);
$price = floatval($_POST['price']);
$stock = intval($_POST['stock']);
$category_id = intval($_POST['category_id']);

// Handle image upload
$image = $_FILES['image']['name'];
$target = "../images/" . basename($image);
move_uploaded_file($_FILES['image']['tmp_name'], $target);

// Insert into database
$query = "INSERT INTO products (name, price, stock, category_id, image) 
          VALUES ('$name', $price, $stock, $category_id, '$image')";

if(mysqli_query($conn, $query)) {
    header("Location: admin_product_inventory.php");
} else {
    echo "Error adding product: " . mysqli_error($conn);
}
?>