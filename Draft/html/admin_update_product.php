<?php
include '../backend/config/db_connect.php';

$id = intval($_POST['product_ID']);
$name = mysqli_real_escape_string($conn, $_POST['name']);
$price = floatval($_POST['price']);
$stock = intval($_POST['stock']);

// Preserve image and description from POST or existing record
$image = mysqli_real_escape_string($conn, $_POST['image'] ?? '');
$description = mysqli_real_escape_string($conn, $_POST['description'] ?? '');

// Fallback to existing record if missing
if (!$image || !$description) {
    $existing = mysqli_fetch_assoc(mysqli_query($conn, "SELECT image, description FROM products WHERE product_ID=$id"));
    $image = $image ?: $existing['image'];
    $description = $description ?: $existing['description'];
}

$query = "UPDATE products 
          SET name='$name', price=$price, stock=$stock, 
              image='$image', description='$description'
          WHERE product_ID=$id";

if (mysqli_query($conn, $query)) {
    header("Location: admin_product_inventory.php");
    exit;
} else {
    echo "Error updating product: " . mysqli_error($conn);
}
?>