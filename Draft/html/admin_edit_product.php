<?php
include '../backend/config/db_connect.php';

$id = intval($_GET['id']);
$query = "SELECT * FROM products WHERE product_ID = $id";
$result = mysqli_query($conn, $query);
$product = mysqli_fetch_assoc($result);

if (!$product) {
    echo "Product not found.";
    exit;
}
?>

<h1>Edit Product</h1>

<form method="POST" action="admin_update_product.php">
    <input type="hidden" name="product_ID" value="<?= $product['product_ID'] ?>">

    Name: <input type="text" name="name" value="<?= $product['name'] ?>"><br><br>
    Price: <input type="number" name="price" value="<?= $product['price'] ?>"><br><br>
    Stock: <input type="number" name="stock" value="<?= $product['stock'] ?>"><br><br>

    <button type="submit">Update</button>
</form>