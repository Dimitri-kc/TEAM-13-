<?php

require_once '../backend/services/userFunctions.php';
require_admin_page('/TEAM-13-/Draft/html/signin.php');

include '../backend/config/db_connect.php';

// Get form data
$name        = mysqli_real_escape_string($conn, $_POST['name']);
$price       = floatval($_POST['price']);
$stock       = intval($_POST['stock']);
$category_id = intval($_POST['category_id']);
$description = mysqli_real_escape_string($conn, $_POST['description']);

// Handle image upload
$image = '';
if(isset($_FILES['image']) && $_FILES['image']['name'] != '') {
    $image = $_FILES['image']['name'];
    $target = "../images/" . basename($image);
    move_uploaded_file($_FILES['image']['tmp_name'], $target);
}

// Insert into database
$query = "INSERT INTO products (name, price, stock, category_id, image, description) 
          VALUES ('$name', $price, $stock, $category_id, '$image', '$description')";

if(mysqli_query($conn, $query)) {
    header("Location: admin_product_inventory.php");
    exit();
} else {
    echo "Error adding product: " . mysqli_error($conn);
}
?>